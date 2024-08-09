window.onload = () => {
	console.log('Station Dev Logger loaded');

	let evtSrc = null;

	function displayEvent({ event, data }) {
		let message = `\x1B[1m ${event.padEnd(13, ' ')}:\x1B[m `;
		if (event.startsWith('API')) {
			const { method, path } = data;
			message += `\x1B[94mWPCLOUD DEV - \x1B[32m${method.padEnd(4, ' ')}\x1B[m ${path}`;

			if (event.endsWith('RESPONSE')) {
				console.log(message, data.response);
			} else {
				if ('POST' === method) {
					console.log(message, data.body)
				} else {
					console.log(message)
				}
			}

			return;
		}

		if ('PAGE_VIEW' === event) {
			message += `${data.url}`
			console.log(message);
			return;
		}
		console.log(message, data);
	}

	function logToIdxDb(data) {
		const dbOpen = window.indexedDB.open("WpCloudLog", 1);

		dbOpen.onsuccess = () => {
			const db = dbOpen.result;
			const transaction = db.transaction(["entries"], "readwrite");
			const objectStore = transaction.objectStore("entries");
			objectStore.add(data);
		}
		dbOpen.onerror = console.log.error;
	}

	function connectToServer(callback) {
		console.log('connection to server');
		evtSrc = new EventSource('/wpcloud-station-dev/sse');
		evtSrc.addEventListener('wpcloud_event', callback);
	}

	function tailEvents() {
		console.log('tailing events');
		return connectToServer( (e) => {
			const data = JSON.parse(e.data);
			if (data) {
				displayEvent(JSON.parse(data));
			}
		} );
	}

	function recordEvents() {
		const dbOpen = window.indexedDB.open("WpCloudLog", 1);

		dbOpen.onerror = console.error

		dbOpen.onupgradeneeded = (e) => {
			console.log('creating object store')
			const db = e.target.result;
			const os = db.createObjectStore("entries", { keyPath: "timestamp" })

			os.createIndex("event", "event", { unique: false });
			os.createIndex("timestamp", "timestamp", { unique: true });
		}

		return connectToServer( (e) => {
			const data = JSON.parse(e.data);
			if (data) logToIdxDb(JSON.parse(data));
		} );
	}

	function replayLog(flush = true) {
		const dbOpen = window.indexedDB.open("WpCloudLog", 1);
		dbOpen.onsuccess = () => {
			const db = dbOpen.result;
			const os = db.transaction('entries', 'readwrite').objectStore('entries');

			os.openCursor().onsuccess = (e) => {
				const cursor = e.target.result;
				if (!cursor) {
					return;
				}
				displayEvent(cursor.value);
				if (flush) {
					cursor.delete();
				}

				cursor.continue();
			}
		}
	}

	window.wpcloudDevLog = {
		tail : tailEvents,
		record: recordEvents,
		replay: replayLog,
		eventSource: evtSrc
	}
};