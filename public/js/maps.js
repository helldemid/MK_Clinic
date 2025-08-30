function initMap() {
	const location = { lat: 51.5425888, lng: 0.6607790 };
	const mapConfig = {
		zoom: 15,
		center: location,
	}

	/**
	 * init Google Maps container
	 * @param {string} containerId
	 */
	const initMap = (containerId) => {
		const container = document.getElementById(containerId);
		if (null === container) return;
		const map = new google.maps.Map(document.getElementById(containerId), mapConfig);
		new google.maps.Marker({
			position: location,
			map: map,
		});
	}

	initMap('map');
	initMap('contactsMap');
}