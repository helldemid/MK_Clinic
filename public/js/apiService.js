const ApiService = {
	/**
	 * Send POST request with JSON body
	 * @param {string} url - endpoint URL
	 * @param {Object} data - payload
	 * @returns {Promise<Object>} response JSON
	 */
	async post(url, data) {
		try {
			const response = await fetch(url, {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-Requested-With": "XMLHttpRequest",
					// если у тебя есть CSRF токен:
					"X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || ""
				},
				body: JSON.stringify(data)
			});

			if (!response.ok) {
				throw new Error(`HTTP error! Status: ${response.status}`);
			}

			return await response.json();
		} catch (error) {
			console.error("ApiService.post error:", error);
			throw error;
		}
	},

	/**
	 * Send DELETE request
	 * @param {string} url - endpoint URL
	 * @returns {Promise<Object>} response JSON
	 */
	async delete(url) {
		try {
			const response = await fetch(url, {
				method: "DELETE",
				headers: { "X-Requested-With": "XMLHttpRequest" }
			});

			if (!response.ok) {
				throw new Error(`HTTP error! Status: ${response.status}`);
			}

			return await response.json();
		} catch (error) {
			console.error("ApiService.delete error:", error);
			throw error;
		}
	}
};

/**
 * Handle API response and show alert
 * @param {Promise} promise - ApiService call
 * @param {string} successMsg
 */
async function handleApiResponse(promise, successMsg = "Operation completed!", showModalOnResult = true) {
	try {
		const result = await promise;

		if (result.success) {
			if (showModalOnResult) {
				// Show modal with result details
				AlertService.success(successMsg);
			}
			return result;
		} else {
			if (showModalOnResult) {
				// Show modal with result details
				AlertService.error(result.message || "Unknown error");
			}
			console.error(result.error);
			return result;
		}
	} catch (error) {
		AlertService.error("Server error. Try again later.");
		return null;
	}
}

