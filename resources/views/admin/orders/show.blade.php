@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Tracking Order</h1>

        <p><b>Kode Order:</b> {{ $order->order_code }}</p>
        <p><b>Status:</b> {{ $order->status }}</p>
        <p><b>Layanan:</b> {{ $order->service_type ?? '-' }}</p>
        <p><b>Mode Layanan:</b> {{ $order->service_mode ?? 'onsite' }}</p>
        <p><b>Problem:</b> {{ $order->problem ?? '-' }}</p>
        <p><b>ETA:</b> {{ $order->eta ? $order->eta . ' menit' : '-' }}</p>
    </div>

    <div class="card">
        <h2>Map Tracking dengan Routing Jalan</h2>
        <p>
            Marker biru = user, orange = mekanik, hijau = bengkel.
            Garis rute diambil dari OSRM agar mengikuti jalan.
        </p>

        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 14px;">
            <div style="background:#eff6ff; padding:10px 14px; border-radius:12px;">
                🔵 User
            </div>

            <div style="background:#fff7ed; padding:10px 14px; border-radius:12px;">
                🟠 Mekanik
            </div>

            <div style="background:#f0fdf4; padding:10px 14px; border-radius:12px;">
                🟢 Bengkel
            </div>

            <div style="background:#f9fafb; padding:10px 14px; border-radius:12px;">
                <span id="activeRouteTitle">Rute Aktif</span>:
                <b id="activeRouteDistance">-</b>
                |
                <span id="activeRouteDuration">-</span>
            </div>
        </div>

        <div id="trackingMap" style="height: 460px; border-radius: 16px; overflow: hidden;"></div>
    </div>

    <div class="grid">
        <div class="card">
            <h3>Lokasi User</h3>
            <p><b>Nama:</b> {{ $order->customer_name }}</p>
            <p><b>No HP:</b> {{ $order->customer_phone ?? '-' }}</p>
            <p><b>Latitude:</b> {{ $order->user_latitude }}</p>
            <p><b>Longitude:</b> {{ $order->user_longitude }}</p>
        </div>

        <div class="card">
            <h3>Lokasi Mekanik</h3>
            <p><b>Nama:</b> {{ $order->mechanic_name ?? '-' }}</p>
            <p><b>No HP:</b> {{ $order->mechanic_phone ?? '-' }}</p>
            <p><b>Latitude:</b> {{ $order->mechanic_latitude }}</p>
            <p><b>Longitude:</b> {{ $order->mechanic_longitude }}</p>
        </div>

        <div class="card">
            <h3>Lokasi Bengkel</h3>
            <p><b>Nama:</b> {{ $order->workshop_name ?? '-' }}</p>
            <p><b>Alamat:</b> {{ $order->workshop_address ?? '-' }}</p>
            <p><b>Latitude:</b> {{ $order->workshop_latitude }}</p>
            <p><b>Longitude:</b> {{ $order->workshop_longitude }}</p>
        </div>

        <div class="card">
            <h3>Biaya</h3>
            <p><b>Biaya Dasar:</b> Rp {{ number_format($order->basic_cost, 0, ',', '.') }}</p>
            <p><b>Total Biaya:</b> Rp {{ number_format($order->total_cost, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="card">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Kembali</a>
        <a href="/api/orders/{{ $order->id }}/tracking" target="_blank" class="btn">Lihat API Tracking</a>
    </div>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        function parseCoord(value) {
            if (value === null || value === undefined || value === '') {
                return null;
            }

            const number = Number(value);

            if (Number.isNaN(number)) {
                return null;
            }

            return number;
        }

        function isValidPoint(point) {
            return point.lat !== null && point.lng !== null;
        }

        function formatDistance(meter) {
            if (meter === null || meter === undefined) {
                return '-';
            }

            if (meter < 1000) {
                return Math.round(meter) + ' m';
            }

            return (meter / 1000).toFixed(2) + ' km';
        }

        function formatDuration(seconds) {
            if (seconds === null || seconds === undefined) {
                return '-';
            }

            const minutes = Math.round(seconds / 60);

            if (minutes < 60) {
                return minutes + ' menit';
            }

            const hours = Math.floor(minutes / 60);
            const restMinutes = minutes % 60;

            return hours + ' jam ' + restMinutes + ' menit';
        }

        function calculateStraightDistanceMeter(pointA, pointB) {
            if (!isValidPoint(pointA) || !isValidPoint(pointB)) {
                return null;
            }

            const earthRadiusMeter = 6371000;

            const lat1 = pointA.lat * Math.PI / 180;
            const lat2 = pointB.lat * Math.PI / 180;
            const deltaLat = (pointB.lat - pointA.lat) * Math.PI / 180;
            const deltaLng = (pointB.lng - pointA.lng) * Math.PI / 180;

            const a =
                Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
                Math.cos(lat1) * Math.cos(lat2) *
                Math.sin(deltaLng / 2) * Math.sin(deltaLng / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return earthRadiusMeter * c;
        }

        function makeIcon(label, color) {
            return L.divIcon({
                className: '',
                html: `
                    <div style="
                        background:${color};
                        color:white;
                        width:38px;
                        height:38px;
                        border-radius:50%;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        border:4px solid white;
                        box-shadow:0 3px 10px #0005;
                        font-weight:bold;
                    ">${label}</div>
                `,
                iconSize: [38, 38],
                iconAnchor: [19, 19],
            });
        }

        const serviceType = @json($order->service_type ?? '-');
        const serviceMode = @json($order->service_mode ?? 'onsite');

        const userLocation = {
            lat: parseCoord(@json($order->user_latitude)),
            lng: parseCoord(@json($order->user_longitude)),
            name: @json($order->customer_name),
            phone: @json($order->customer_phone ?? '-'),
        };

        const mechanicLocation = {
            lat: parseCoord(@json($order->mechanic_latitude)),
            lng: parseCoord(@json($order->mechanic_longitude)),
            name: @json($order->mechanic_name ?? 'Mekanik'),
            phone: @json($order->mechanic_phone ?? '-'),
        };

        const workshopLocation = {
            lat: parseCoord(@json($order->workshop_latitude)),
            lng: parseCoord(@json($order->workshop_longitude)),
            name: @json($order->workshop_name ?? 'Bengkel'),
            address: @json($order->workshop_address ?? '-'),
        };

        const defaultCenter = isValidPoint(userLocation)
            ? [userLocation.lat, userLocation.lng]
            : [-7.1167, 112.4167];

        const map = L.map('trackingMap').setView(defaultCenter, 15);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const userIcon = makeIcon('U', '#2563eb');
        const mechanicIcon = makeIcon('M', '#f97316');
        const workshopIcon = makeIcon('B', '#16a34a');

        const bounds = [];

        if (isValidPoint(userLocation)) {
            L.marker([userLocation.lat, userLocation.lng], { icon: userIcon })
                .addTo(map)
                .bindPopup(`
                    <b>Lokasi User</b><br>
                    Nama: ${userLocation.name}<br>
                    No HP: ${userLocation.phone}<br>
                    Koordinat: ${userLocation.lat}, ${userLocation.lng}
                `);

            bounds.push([userLocation.lat, userLocation.lng]);
        }

        if (isValidPoint(mechanicLocation)) {
            L.marker([mechanicLocation.lat, mechanicLocation.lng], { icon: mechanicIcon })
                .addTo(map)
                .bindPopup(`
                    <b>Lokasi Mekanik</b><br>
                    Nama: ${mechanicLocation.name}<br>
                    No HP: ${mechanicLocation.phone}<br>
                    Koordinat: ${mechanicLocation.lat}, ${mechanicLocation.lng}
                `);

            bounds.push([mechanicLocation.lat, mechanicLocation.lng]);
        }

        if (isValidPoint(workshopLocation)) {
            L.marker([workshopLocation.lat, workshopLocation.lng], { icon: workshopIcon })
                .addTo(map)
                .bindPopup(`
                    <b>Lokasi Bengkel</b><br>
                    Nama: ${workshopLocation.name}<br>
                    Alamat: ${workshopLocation.address}<br>
                    Koordinat: ${workshopLocation.lat}, ${workshopLocation.lng}
                `);

            bounds.push([workshopLocation.lat, workshopLocation.lng]);
        }

        if (bounds.length > 1) {
            map.fitBounds(bounds, {
                padding: [60, 60],
            });
        }

        async function drawOsrmRoute(from, to, color, label, distanceElementId, durationElementId) {
            if (!isValidPoint(from) || !isValidPoint(to)) {
                document.getElementById(distanceElementId).innerText = '-';
                document.getElementById(durationElementId).innerText = '-';
                return;
            }

            const osrmUrl =
                `https://router.project-osrm.org/route/v1/driving/` +
                `${from.lng},${from.lat};${to.lng},${to.lat}` +
                `?overview=full&geometries=geojson`;

            try {
                const response = await fetch(osrmUrl);
                const data = await response.json();

                if (!data.routes || data.routes.length === 0) {
                    throw new Error('Rute tidak ditemukan');
                }

                const route = data.routes[0];

                const routeCoordinates = route.geometry.coordinates.map((coordinate) => {
                    return [coordinate[1], coordinate[0]];
                });

                L.polyline(routeCoordinates, {
                    color: color,
                    weight: 5,
                    opacity: 0.85,
                })
                .addTo(map)
                .bindPopup(`
                    <b>${label}</b><br>
                    Jarak jalan: ${formatDistance(route.distance)}<br>
                    Estimasi waktu: ${formatDuration(route.duration)}
                `);

                document.getElementById(distanceElementId).innerText =
                    formatDistance(route.distance);

                document.getElementById(durationElementId).innerText =
                    formatDuration(route.duration);

            } catch (error) {
                console.error(error);

                L.polyline(
                    [
                        [from.lat, from.lng],
                        [to.lat, to.lng],
                    ],
                    {
                        color: color,
                        weight: 4,
                        opacity: 0.7,
                        dashArray: '8, 8',
                    }
                )
                .addTo(map)
                .bindPopup(`
                    <b>${label}</b><br>
                    Rute jalan gagal dimuat. Menampilkan garis lurus.
                `);

                const straightDistance = calculateStraightDistanceMeter(from, to);

                document.getElementById(distanceElementId).innerText =
                    formatDistance(straightDistance);

                document.getElementById(durationElementId).innerText =
                    'estimasi rute gagal';
            }
        }

        function setActiveRouteTitle(title) {
            document.getElementById('activeRouteTitle').innerText = title;
        }

        if (serviceMode === 'onsite') {
            setActiveRouteTitle('Rute Mekanik → User');

            drawOsrmRoute(
                mechanicLocation,
                userLocation,
                '#f97316',
                'Rute Mekanik ke User',
                'activeRouteDistance',
                'activeRouteDuration'
            );
        } else if (serviceMode === 'workshop') {
            setActiveRouteTitle('Rute User → Bengkel');

            drawOsrmRoute(
                userLocation,
                workshopLocation,
                '#16a34a',
                'Rute User ke Bengkel',
                'activeRouteDistance',
                'activeRouteDuration'
            );
        } else {
            setActiveRouteTitle('Rute Tidak Diketahui');

            document.getElementById('activeRouteDistance').innerText = '-';
            document.getElementById('activeRouteDuration').innerText = '-';
        }
    </script>
@endsection
