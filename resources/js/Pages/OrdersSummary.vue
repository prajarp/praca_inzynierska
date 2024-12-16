<template>
    <div class="flex flex-col h-[90vh]">
        <div>
            navbar
        </div>
        <div id="map" class="flex-1">
        </div>
        <!-- <h2>
            <strong>
                {{ selectedOrders.original.length }}
            </strong>
        </h2> -->
    </div>
    <div class="mt-4 mr-10 flex justify-end">
        <div>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="sentToPacking">
                Przygotuj zamówienie
            </button>
        </div>
    </div>
</template>
<script setup>

import { defineProps, onMounted } from 'vue';
const props = defineProps({
    selectedOrders: Object,
});

const TOMTOM_API_KEY = import.meta.env.VITE_API_KEY;

function resetMap(map) {
    map.remove(); // Usuń mapę
    initializeMap(); // Funkcja inicjalizująca mapę
}

function initializeMap() {
    const tt = window.tt;

    if (!props.selectedOrders || !props.selectedOrders.original?.length) {
        console.error("Brak danych do wyświetlenia na mapie.");
        return;
    }

    const map = tt.map({
        key: TOMTOM_API_KEY,
        container: 'map',
        center: [props.selectedOrders.original[0].longitude, props.selectedOrders.original[0].latitude],
        zoom: 6
    });

    map.addControl(new tt.NavigationControl());

    let index = 1;
    props.selectedOrders.original.slice(1).forEach((point) => {
        // Tworzenie niestandardowego elementu HTML
        const customElement = document.createElement('div');
        customElement.className = 'custom-marker';
        customElement.innerText = index;  // Ustawienie numeru punktu

        // Dodanie znacznika do mapy
        new tt.Marker({ element: customElement })
            .setLngLat([point.longitude, point.latitude])
            .addTo(map);

        index++;
    });

    drawMap(map, props.selectedOrders.original, 'routeDemo', '#4a90a2'); // Niebieska trasa
}

async function drawMap(map, selectedOrders, layerId, color = '#4a90a2') {
    try {
        const tt = window.tt;

        if (!tt.services || !tt.services.calculateRoute) {
            console.error("TomTom Routing Services są niedostępne.");
            return;
        }

        const response = await tt.services.calculateRoute({
            key: TOMTOM_API_KEY,
            locations: selectedOrders.map(coord => `${coord.longitude},${coord.latitude}`).join(':'),
        });

        const geojsonObj = response.toGeoJson();
        const boundaryCoords = selectedOrders.map(coord => [coord.longitude, coord.latitude]);
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
        resetMap(map);
        // redrawMap();
    }
}

function setMapBounds(map, selectedOrders) {
    const bounds = new tt.LngLatBounds();
    selectedOrders.forEach(coord => bounds.extend(coord));
    map.fitBounds(bounds, { padding: 20 });
}

function redrawMap() {
    const mapContainer = document.getElementById('map');
    mapContainer.innerHTML = ''; // Wyczyść mapę
    initializeMap(); // Narysuj mapę na nowo
}


onMounted(() => {
    initializeMap();
});

const sentToPacking = () => {
  router.get('/packing');
};
</script>

<style scoped>
.custom-marker {
    width: 100px;
    height: 100px;
    background-color: #4a90a2;
    color: white;
    font-size: 72px;
    font-weight: bold;
    text-align: center;
    line-height: 30px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
}
</style>