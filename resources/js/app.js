const InventoryUI = (() => {
	const formatTime = (isoString) => {
		if (!isoString) {
			return '-';
		}
		const date = new Date(isoString);
		if (Number.isNaN(date.getTime())) {
			return '-';
		}
		return new Intl.DateTimeFormat('id-ID', {
			dateStyle: 'medium',
			timeStyle: 'medium',
		}).format(date);
	};

	const setLoading = (isLoading) => {
		const indicator = document.getElementById('global-loading');
		if (!indicator) {
			return;
		}
		indicator.classList.toggle('hidden', !isLoading);
	};

	const toast = (message, type = 'info') => {
		const container = document.getElementById('toast-container');
		if (!container) {
			return;
		}

		const toastEl = document.createElement('div');
		const baseClasses =
			'pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-lg transition';
		const typeClasses = {
			info: 'border-slate-200 bg-white text-slate-800',
			success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
			warning: 'border-amber-200 bg-amber-50 text-amber-900',
			danger: 'border-rose-200 bg-rose-50 text-rose-900',
		};

		toastEl.className = `${baseClasses} ${typeClasses[type] ?? typeClasses.info}`;

		const dot = document.createElement('span');
		dot.className = 'mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-current opacity-70';

		const text = document.createElement('div');
		text.className = 'leading-5';
		text.textContent = message;

		toastEl.append(dot, text);

		container.appendChild(toastEl);

		setTimeout(() => {
			toastEl.classList.add('opacity-0', 'translate-y-1');
		}, 2600);

		setTimeout(() => {
			toastEl.remove();
		}, 3100);
	};

	const flashRow = (row) => {
		if (!row) {
			return;
		}
		row.classList.add('bg-amber-50');
		setTimeout(() => {
			row.classList.remove('bg-amber-50');
		}, 1500);
	};

	return {
		formatTime,
		setLoading,
		toast,
		flashRow,
	};
})();

window.InventoryUI = InventoryUI;
