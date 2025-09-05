import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static targets = ["timeline", "loadMore"];
	offset = 0;
	limit = 5;
	end = false;

	connect() {
		this.load();
	}

	async load() {
		if (this.end) return;
		this.loadMoreTarget.disabled = true;
		const response = await fetch(`/account/appointments/history?offset=${this.offset}&limit=${this.limit}`);
		const data = await response.json();

		// if no items at all
		if (this.offset === 0 && data.items.length === 0) {
			const msg = document.createElement('div');
			msg.className = 'timeline-empty';
			msg.innerText = 'You have no appointment history.';
			this.timelineTarget.appendChild(msg);
			this.loadMoreTarget.style.display = 'none';
			this.end = true;
			return;
		}

		data.items.forEach(item => {
			const div = document.createElement('div');
			div.innerHTML = item.html;
			this.timelineTarget.appendChild(div.firstElementChild);
		});

		this.offset += data.items.length;
		this.loadMoreTarget.disabled = false;
		if (!data.hasMore) {
			this.end = true;
			this.loadMoreTarget.style.display = 'none';
		}
	}
}