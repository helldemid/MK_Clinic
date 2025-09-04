import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static values = { url: String }

	show(event) {
		event.preventDefault();
		const requestId = event.currentTarget.dataset.requestId;
		fetch(`/admin/request/${requestId}/details`, {
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(res => res.json())
			.then(data => {
				if (data.success) {
					AlertService.htmlModalTemplate(data.html, 'Request Details', false);
				} else {
					Swal.fire('Error', data.error || 'Request not found', 'error');
				}
			});
	}
}