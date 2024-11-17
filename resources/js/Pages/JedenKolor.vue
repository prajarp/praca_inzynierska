<script setup>
import { defineProps, onMounted } from 'vue';

const props = defineProps({
  coordinates: Array
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

// Funkcja inicjalizująca mapę i dodająca markery oraz trasę
function initializeMap() {
  const tt = window.tt;

  const map = tt.map({
    key: TOMTOM_API_KEY,
    container: 'map',
    center: [props.coordinates[0].longitude, props.coordinates[0].latitude],
    zoom: 6
  });

  map.addControl(new tt.NavigationControl());

  map.doubleClickZoom.disable();
  console.log("Czy doubleClickZoom jest włączone?:", map.doubleClickZoom.isEnabled());

  props.coordinates.forEach((point) => {
    const marker = new tt.Marker()
      .setLngLat([point.longitude, point.latitude])
      .addTo(map);

    const popup = new tt.Popup({ offset: 35 }).setText(point.address);
    marker.setPopup(popup);
  });

  drawMap(map);
}

// Funkcja rysująca trasę zoptymalizowaną dla ciężarówki na mapie
async function drawMap(map) {
  try {
    const tt = window.tt;

    if (!tt.services || !tt.services.calculateRoute) {
      console.error("TomTom Routing Services są niedostępne.");
      return;
    }

    // Usunięcie .go() i bezpośrednie czekanie na wynik
    const response = await tt.services.calculateRoute({
      key: TOMTOM_API_KEY,
      locations: props.coordinates.map(coord => `${coord.longitude},${coord.latitude}`).join(':'),
      ...truckConfig,
    });

    const geojsonObj = response.toGeoJson();
    const boundaryCoords = props.coordinates.map(coord => [coord.longitude, coord.latitude]);
    setMapBounds(map, boundaryCoords);

    map.addLayer({
      id: 'routeDemo',
      type: 'line',
      source: {
        type: 'geojson',
        data: geojsonObj,
      },
      paint: {
        'line-color': '#4a90a2',
        'line-width': 6,
      },
    });
  } catch (error) {
    console.error("Błąd podczas rysowania trasy na mapie:", error);
  }
}

// Funkcja ustawiająca zasięg mapy na podstawie podanych współrzędnych
function setMapBounds(map, coordinates) {
  const bounds = new tt.LngLatBounds();
  coordinates.forEach(coord => bounds.extend(coord));
  map.fitBounds(bounds, { padding: 20 });
}

// Wykonujemy funkcję initializeMap po zamontowaniu komponentu
onMounted(() => {
  initializeMap();
});
</script>

<template>
  <div id="map" style="width: 100%; height: 500px;"></div>
</template>


<style>
#map {
  width: 100%;
  height: 600px;
}
</style>