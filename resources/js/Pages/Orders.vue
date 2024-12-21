<template>
  <Navbar></Navbar>
  <div class="flex w-full h-screen">
    <div id="lista" class="w-1/4 bg-gray-200 p-4">
      <h2 class="font-bold mb-2">Lista Punktów</h2>

      <div v-for="(point, index) in reorderedCoordinates" :key="index" class="mb-4 border-b pb-2">
        <p><strong>{{ index }}. {{ point.address }}</strong></p>
        <div class="space-x-2 mt-2">
          <button
            @click="moveUp(index)"
            :disabled="index === 0"
            class="bg-blue-500 text-white px-2 py-1 rounded disabled:bg-gray-400"
          >
            ↑
          </button>
          <button
            @click="moveDown(index)"
            :disabled="index === reorderedCoordinates.length - 1"
            class="bg-blue-500 text-white px-2 py-1 rounded disabled:bg-gray-400"
          >
            ↓
          </button>
        </div>
      </div>
      <button class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2 text-center" @click="sendToAnotherView">
            Zaktualizuj trasę
          </button>
    </div>
    <div id="remainingSpace" class="w-3/4 bg-gray-100">
      <div id="map" class="w-full h-full"></div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, onMounted } from 'vue';
import { ref } from 'vue';
import Navbar from '../components/Navbar.vue';
import { router } from '@inertiajs/vue3';


const props = defineProps({
  coordinates: Object,
});

const TOMTOM_API_KEY = import.meta.env.VITE_API_KEY;

const reorderedCoordinates = props.coordinates;

const truckConfig = {
  vehicleMaxHeight: 4.0,
  vehicleMaxWidth: 2.5,
  vehicleMaxLength: 18.0,
  vehicleWeight: 40000,
  vehicleAxleWeight: 10000,
  vehicleCommercial: true,
};

function resetMap(map) {
  map.remove();
  initializeMap();
}

function initializeMap() {
  if (!reorderedCoordinates || reorderedCoordinates.length === 0) {
        console.error('Brak punktów do wyświetlenia na mapie.');
        return;
    }

    const tt = window.tt;

    const map = tt.map({
        key: TOMTOM_API_KEY,
        container: 'map',
        center: [
            reorderedCoordinates[0].longitude,
            reorderedCoordinates[0].latitude,
        ],
        zoom: 6,
    });

    map.addControl(new tt.NavigationControl());

    drawMarkersWithRoutes(map, reorderedCoordinates)
        .then(() => console.log('Markery zostały narysowane.'))
        .catch(error => console.error('Błąd przy rysowaniu markerów:', error));
  // reorderedCoordinates.forEach((point, index) => {

  //   const customElement = document.createElement('div');
  //   customElement.className = 'custom-marker';
  //   customElement.innerText = index;

  //   customElement.style.width = '1px';
  //   customElement.style.height = '10px';
  //   customElement.style.lineHeight = '10px';
  //   customElement.style.fontSize = '24px';

  //   const marker = new tt.Marker({ element: customElement })
  //     .setLngLat([point.longitude, point.latitude])
  //     .addTo(map);

  //   let popupText = `${index}. ${point.address}`;

  //   if (point.travel_info && Array.isArray(point.travel_info)) {
  //     const travelInfo = point.travel_info[0];
  //     if (travelInfo && travelInfo.distance_in_km) {
  //       popupText += '\n\n';
  //       popupText += `From: ${travelInfo.from} To: ${travelInfo.to}`;
  //       popupText += ` - Distance: ${travelInfo.distance_in_km} km`;
  //       popupText += ` - In: ${travelInfo.travel_time_in_minutes} minutes`;
  //       popupText += `\n\n ORDER ID ${point.order_id}`;
  //     }
  //   }

  //   const popup = new tt.Popup({ offset: 35 }).setText(popupText);
  //   marker.setPopup(popup);
  // });

  drawMap(map, reorderedCoordinates, 'routeDemo', '#4a90a2');
}

async function drawMap(map, coordinates, layerId, color = '#4a90a2') {
  try {
    const tt = window.tt;

    if (!tt.services || !tt.services.calculateRoute) {
      console.error('TomTom Routing Services są niedostępne.');
      return;
    }

    const response = await tt.services.calculateRoute({
      key: TOMTOM_API_KEY,
      locations: coordinates
        .map((coord) => `${coord.longitude},${coord.latitude}`)
        .join(':'),
      ...truckConfig,
    });

    const geojsonObj = response.toGeoJson();
    const boundaryCoords = coordinates.map((coord) => [
      coord.longitude,
      coord.latitude,
    ]);
    setMapBounds(map, boundaryCoords);

    map.addLayer({
      id: layerId,
      type: 'line',
      source: {
        type: 'geojson',
        data: geojsonObj,
      },
      paint: {
        'line-color': color,
        'line-width': 4,
      },
    });
  } catch (error) {
    console.error(`Błąd podczas rysowania trasy dla warstwy ${layerId}:`, error);
    if(error) {
      redrawMap();

    }
  }
}

function setMapBounds(map, coordinates) {
  const bounds = new tt.LngLatBounds();
  coordinates.forEach((coord) => bounds.extend(coord));
  map.fitBounds(bounds, { padding: 20 });
}

function moveUp(index) {
  if (index > 0) {
    [reorderedCoordinates[index - 1], reorderedCoordinates[index]] = [
      reorderedCoordinates[index],
      reorderedCoordinates[index - 1],
    ];
    redrawMap();
  }
}

function moveDown(index) {
  if (index < reorderedCoordinates.length - 1) {
    [reorderedCoordinates[index], reorderedCoordinates[index + 1]] = [
      reorderedCoordinates[index + 1],
      reorderedCoordinates[index],
    ];
    redrawMap();
  }
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function calculateRoute(reorderedCoordinates) {
    const apiKey = TOMTOM_API_KEY;
    const results = [];

    for (let i = 0; i < reorderedCoordinates.length - 1; i++) {
        const origin = reorderedCoordinates[i];
        const destination = reorderedCoordinates[i + 1];

        const url = `https://api.tomtom.com/routing/1/calculateRoute/${origin.latitude},${origin.longitude}:${destination.latitude},${destination.longitude}/json`;

        try {
            const response = await fetch(`${url}?key=${apiKey}&routeType=fastest&travelMode=truck&traffic=true`);

            if (!response.ok) {
                results.push({
                    from: origin.address,
                    to: destination.address,
                    error: 'Nie udało się obliczyć trasy.'
                });
                continue;
            }

            const routeData = await response.json();

            if (!routeData.routes || !routeData.routes[0] || !routeData.routes[0].summary) {
                results.push({
                    from: origin.address,
                    to: destination.address,
                    error: 'Brak danych o trasie.'
                });
                continue;
            }

            const routeSummary = routeData.routes[0].summary;

            results.push({
                from: origin.address,
                to: destination.address,
                distance_in_km: (routeSummary.lengthInMeters / 1000).toFixed(2),
                travel_time_in_minutes: (routeSummary.travelTimeInSeconds / 60).toFixed(2),
                traffic_delay_in_minutes: (routeSummary.trafficDelayInSeconds / 60).toFixed(2)
            });
        } catch (error) {
            results.push({
                from: origin.address,
                to: destination.address,
                error: 'Wystąpił błąd w trakcie obliczania trasy.'
            });
        }
        await sleep(100);
    }

    return results;
}

async function drawMarkersWithRoutes(map, points) {
    const routes = await calculateRoute(points);

points.forEach((point, index) => {
    if (index === 0) {
        console.log(`Pomijam pierwszy punkt: ${point.address}`);
        return; 
    }

    if (!point.latitude || !point.longitude) {
        console.warn(`Punkt ${index} nie ma współrzędnych. Pomijam.`);
        return;
    }

    const customElement = document.createElement('div');
    customElement.className = 'custom-marker';
        customElement.innerText = index;

      customElement.style.width = '1px';
      customElement.style.height = '10px';
      customElement.style.lineHeight = '10px';
      customElement.style.fontSize = '24px';

        const marker = new tt.Marker({ element: customElement })
            .setLngLat([point.longitude, point.latitude])
            .addTo(map);

        const previousPoint = points[index - 1];
        const travelInfo = routes.find(route => 
            route.from === previousPoint.address && route.to === point.address
        );

        let popupText = `${index}. ${point.address}`;

        if (travelInfo) {
            popupText += `\n\nFrom: ${travelInfo.from} To: ${travelInfo.to}`;
            popupText += ` - Distance: ${travelInfo.distance_in_km} km`;
            popupText += ` - In: ${travelInfo.travel_time_in_minutes} minutes`;
            popupText += `\n\n ORDER ID: ${point.order_id || 'N/A'}`;
        } else {
            popupText += `\n\nBrak informacji o trasie z poprzedniego punktu.`;
        }

        const popup = new tt.Popup({ offset: 35 }).setText(popupText);
        marker.setPopup(popup);

    });
} 


function redrawMap() {
  const mapContainer = document.getElementById('map');
  mapContainer.innerHTML = '';
  initializeMap();
}

onMounted(() => {
  initializeMap();
});

const sendToAnotherView = () => {
  router.post('/orders', { selected: reorderedCoordinates });
};
</script>

<style scoped>
.custom-marker {
    width: 300px;
    height: 300px;
    background-color: white;
    color: #4a90a2;
    font-size: 72px;
    font-weight: bold;
    text-align: center;
    line-height: 300px;
    border-radius: 50%;
    border: 5px solid #4a90a2;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}
</style>