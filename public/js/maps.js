async function initMap() {
	const placeId = 'ChIJmeMekAfb2EcRO15Oxk5SDdw';

	// load libraries
	const { Map } = await google.maps.importLibrary("maps");
	const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
	const { Place } = await google.maps.importLibrary("places");

	const mapConfig = {
		zoom: 16,
		center: { lat: 0, lng: 0 },
	};

	const initContainer = async (containerId) => {
		const el = document.getElementById(containerId);
		if (!el) return;

		const map = new Map(el, {
			zoom: 16,
			center: { lat: 0, lng: 0 },
			mapId: "4cf5ca92a69e0e7f72eecbdf",
		});


		// create Place
		const place = new Place({
			id: placeId,
			requestedLanguage: "en",
		});

		// request the necessary fields
		await place.fetchFields({
			fields: ['location', 'displayName', 'formattedAddress']
		});

		// center the map
		map.setCenter(place.location);

		// create AdvancedMarkerElement
		const marker = new AdvancedMarkerElement({
			map,
			position: place.location,
		});

		// create standard InfoWindow
		const infoWindow = new google.maps.InfoWindow({
			content: `
				<div style="max-width:200px; color:#000;">
					<strong>${place.displayName || ''}</strong><br>
					${place.formattedAddress || ''}<br><br>
					<a href="https://maps.app.goo.gl/842Yxuxdt8QUfx6k9"
					target="_blank"
					style="color:#1a73e8; text-decoration:none;">
						Open in Google Maps →
					</a>
				</div>
			`,
		});


		// auto-open
		infoWindow.open(map, marker);

		// and on click too
		marker.addListener('click', () => infoWindow.open(map, marker));
	};

	await initContainer('map');
	await initContainer('contactsMap');
}