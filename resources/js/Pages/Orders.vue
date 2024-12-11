<template>
  <div class="flex w-full h-screen">
    <!-- Sekcja listy -->
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
    </div>

    <!-- Sekcja mapy -->
    <div id="remainingSpace" class="w-3/4 bg-gray-100">
      <div id="map" class="w-full h-full"></div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, ref, onMounted, watch } from 'vue';

const props = defineProps({
  coordinates: Array,
});

const TOMTOM_API_KEY = import.meta.env.VITE_API_KEY;

// Przechowywanie zmiennej z trasą
const reorderedCoordinates = ref([...props.coordinates.original]);

const truckConfig = {
  vehicleMaxHeight: 4.0,
  vehicleMaxWidth: 2.5,
  vehicleMaxLength: 18.0,
  vehicleWeight: 40000,
  vehicleAxleWeight: 10000,
  vehicleCommercial: true,
};

// Funkcja inicjalizacji mapy
function initializeMap() {
  const tt = window.tt;

  const map = tt.map({
    key: TOMTOM_API_KEY,
    container: 'map',
    center: [
      reorderedCoordinates.value[0].longitude,
      reorderedCoordinates.value[0].latitude,
    ],
    zoom: 6,
  });

  map.addControl(new tt.NavigationControl());

  reorderedCoordinates.value.forEach((point, index) => {
    const marker = new tt.Marker()
      .setLngLat([point.longitude, point.latitude])
      .addTo(map);

    let popupText = `${index}. ${point.address}`;
    const popup = new tt.Popup({ offset: 35 }).setText(popupText);
    marker.setPopup(popup);
  });

  drawMap(map, reorderedCoordinates.value, 'routeDemo', '#4a90a2');
}

// Rysowanie trasy
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
        'line-color': color, // Kolor trasy
        'line-width': 4,
      },
    });
  } catch (error) {
    console.error(`Błąd podczas rysowania trasy:`, error);
    if(error) {
      location.reload();
      // redrawMap();
    }
  }
}

// Ustawianie granic mapy
function setMapBounds(map, coordinates) {
  const bounds = new tt.LngLatBounds();
  coordinates.forEach((coord) => bounds.extend(coord));
  map.fitBounds(bounds, { padding: 20 });
}

// Obsługa zmiany kolejności punktów
function moveUp(index) {
  if (index > 0) {
    [reorderedCoordinates.value[index - 1], reorderedCoordinates.value[index]] = [
      reorderedCoordinates.value[index],
      reorderedCoordinates.value[index - 1],
    ];
    redrawMap();
  }
}

function moveDown(index) {
  if (index < reorderedCoordinates.value.length - 1) {
    [reorderedCoordinates.value[index], reorderedCoordinates.value[index + 1]] = [
      reorderedCoordinates.value[index + 1],
      reorderedCoordinates.value[index],
    ];
    redrawMap();
  }
}

// Ponowne rysowanie mapy
function redrawMap() {
  const mapContainer = document.getElementById('map');
  mapContainer.innerHTML = ''; // Wyczyść mapę
  initializeMap(); // Narysuj mapę na nowo
}

// Inicjalizacja mapy przy załadowaniu widoku
onMounted(() => {
  initializeMap();
});
</script>

<style scoped>
button:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}
</style>
