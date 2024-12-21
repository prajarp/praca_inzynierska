<template>
    <div class="flex flex-col h-[90vh]">
        <div id="map" class="flex-1"></div>
    </div>
    <div class="mt-4 mr-10 flex justify-end">
        <div>
            <button
                class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-xl px-5 py-3 text-center"
                @click="sentToPacking">
                Przygotuj zamówienie
            </button>
        </div>
    </div>
</template>
<script setup>

import { defineProps, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
const props = defineProps({
    selectedOrders: Object,
});

const TOMTOM_API_KEY = import.meta.env.VITE_API_KEY;

function resetMap(map) {
    map.remove();
    initializeMap();
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

            let popupText = `${index}. ${point.address}`;
            const popup = new tt.Popup({ offset: 35 }).setText(popupText);
            marker.setPopup(popup);
        index++;
    });

    drawMap(map, props.selectedOrders.original, 'routeDemo', '#4a90a2');
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
                'line-color': color,
                'line-width': 2,
            },
        });
    } catch (error) {
        console.error(`Błąd podczas rysowania trasy dla warstwy ${layerId}:`, error);
        resetMap(map);

    }
}

function setMapBounds(map, selectedOrders) {
    const bounds = new tt.LngLatBounds();
    selectedOrders.forEach(coord => bounds.extend(coord));
    map.fitBounds(bounds, { padding: 20 });
}

function redrawMap() {
    const mapContainer = document.getElementById('map');
    mapContainer.innerHTML = '';
    initializeMap();
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