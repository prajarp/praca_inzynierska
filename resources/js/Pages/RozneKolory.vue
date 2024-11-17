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

// Kolory dla poszczególnych odcinków trasy
const routeColors = ['#ff69b4', '#ff4500', '#32cd32', '#1e90ff'];

// Funkcja inicjalizująca mapę i dodająca markery oraz trasy w różnych kolorach
function initializeMap() {
  const tt = window.tt;

  const map = tt.map({
    key: TOMTOM_API_KEY,
    container: 'map',
    center: [props.coordinates[0].longitude, props.coordinates[0].latitude],
    zoom: 6
  });

  map.addControl(new tt.NavigationControl());

  // Nasłuchujemy zdarzenia `load`, aby mieć pewność, że mapa jest w pełni załadowana
  map.on('load', () => {
    // Iteracja przez współrzędne i dodanie markerów
    props.coordinates.forEach((point) => {
      const marker = new tt.Marker()
        .setLngLat([point.longitude, point.latitude])
        .addTo(map);

      const popup = new tt.Popup({ offset: 35 }).setText(point.address);
      marker.setPopup(popup);
    });

    // Rysujemy trasy między punktami dopiero po załadowaniu mapy
    drawRoutes(map);
  });
}

// Funkcja rysująca trasy w różnych kolorach na mapie
async function drawRoutes(map) {
  const tt = window.tt;

  for (let i = 0; i < props.coordinates.length - 1; i++) {
    const start = props.coordinates[i];
    const end = props.coordinates[i + 1];

    try {
      // Wywołanie API TomTom Routing dla pary punktów start i end
      const response = await tt.services.calculateRoute({
        key: TOMTOM_API_KEY,
        locations: `${start.longitude},${start.latitude}:${end.longitude},${end.latitude}`,
        ...truckConfig,
      });

      const geojsonObj = response.toGeoJson();
      const color = routeColors[i % routeColors.length]; // wybieramy kolor dla odcinka

      // Dodajemy odcinek trasy jako osobną warstwę linii
      map.addLayer({
        id: `routeSegment${i}`,
        type: 'line',
        source: {
          type: 'geojson',
          data: geojsonObj,
        },
        paint: {
          'line-color': color,
          'line-width': 6,
        },
      });
    } catch (error) {
      console.error(`Błąd podczas rysowania trasy odcinka ${i}:`, error);
    }
  }

  // Dopasowanie widoku mapy do wszystkich punktów
  const boundaryCoords = props.coordinates.map(coord => [coord.longitude, coord.latitude]);
  setMapBounds(map, boundaryCoords);
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
