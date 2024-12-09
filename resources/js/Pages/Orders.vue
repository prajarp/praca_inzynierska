<script setup>
import { defineProps, onMounted } from 'vue';
import { ref } from 'vue';
import axios from 'axios';
import Navbar from '../components/Navbar.vue';
const props = defineProps({
  coordinates: Object,
});
const TOMTOM_API_KEY = import.meta.env.VITE_API_KEY;

const truckConfig = {
  vehicleMaxHeight: 4.0,
  vehicleMaxWidth: 2.5,
  vehicleMaxLength: 18.0,
  vehicleWeight: 40000,
  vehicleAxleWeight: 10000,
  vehicleCommercial: true,
};

function resetMap(map) {
  map.remove(); // Usuń mapę
  initializeMap();    // Funkcja inicjalizująca mapę
}

function initializeMap() {
  const tt = window.tt;

  const map = tt.map({
    key: TOMTOM_API_KEY,
    container: 'map',
    center: [props.coordinates.original[0].longitude, props.coordinates.original[0].latitude],
    zoom: 6
  });

  map.addControl(new tt.NavigationControl());

  let index = 0;
  props.coordinates.original.forEach((point) => {
    const marker = new tt.Marker()
      .setLngLat([point.longitude, point.latitude])
      .addTo(map);

    let popupText = `${index}. ${point.address}`;

    if (point.travel_info && Array.isArray(point.travel_info)) {
      const travelInfo = point.travel_info[0];
      if (travelInfo && travelInfo.distance_in_km) {
        popupText += '\n\n';
        popupText += `From: ${travelInfo.from} To: ${travelInfo.to}`;
        popupText += ` - Distance: ${travelInfo.distance_in_km} km`;
        popupText += ` - In: ${travelInfo.travel_time_in_minutes} minutes`;
        popupText += `\n\n ORDER ID ${point.order_id}`;
      }
    }

    const popup = new tt.Popup({ offset: 35 }).setText(popupText);
    marker.setPopup(popup);

    index++;
  });

  drawMap(map, props.coordinates.original, 'routeDemo', '#4a90a2'); // Niebieska trasa
}

async function drawMap(map, coordinates, layerId, color = '#4a90a2') {
  try {
    const tt = window.tt;

    if (!tt.services || !tt.services.calculateRoute) {
      console.error("TomTom Routing Services są niedostępne.");
      return;
    }

    const response = await tt.services.calculateRoute({
      key: TOMTOM_API_KEY,
      locations: coordinates.map(coord => `${coord.longitude},${coord.latitude}`).join(':'),
      ...truckConfig,
    });

    const geojsonObj = response.toGeoJson();
    const boundaryCoords = coordinates.map(coord => [coord.longitude, coord.latitude]);
    setMapBounds(map, boundaryCoords);

    map.addLayer({
      id: layerId,
      type: 'line',
      source: {
        type: 'geojson',
        data: geojsonObj,
      },
      paint: {
        'line-color': color, // Użycie koloru przekazanego do funkcji
        'line-width': 2,
      },
    });
  } catch (error) {
    console.error(`Błąd podczas rysowania trasy dla warstwy ${layerId}:`, error);
    if(error) {
      resetMap(map);
    }
  }
}



function setMapBounds(map, coordinates) {
  const bounds = new tt.LngLatBounds();
  coordinates.forEach(coord => bounds.extend(coord));
  map.fitBounds(bounds, { padding: 20 });
}

onMounted(() => {
  initializeMap();
});
</script>



<template>
  <div class="flex flex-col h-screen">
    <div>
      <Navbar />
    </div>
    <div id="map" class="flex-1">
      Tu będzie mapa
    </div>
  </div>
</template>


<style>
#map {
  width: 100%;
  height: 600px;
}
</style>