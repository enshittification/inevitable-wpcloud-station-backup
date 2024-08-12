window.onload = () => {
	console.log('Station Dev Logger loaded');

	let evtSrc = null;

	function displayEvent({ event, data }) {

		let eventMessage = `\x1B[94m[WPCLOUD DEV::\x1B[1m${event}]\x1B[m`;
		logMessage = eventMessage.padEnd(40, ' ');

		// Log API events
		if (event.startsWith('API')) {
			const { method, path } = data;
			const showMethod = event.endsWith('REQUEST') ? method : ' >>>';
			logMessage += `\x1B[92m${showMethod.padEnd(4, ' ')}\x1B[m ${path}`;

			if (event.endsWith('RESPONSE')) {
				code = data.response.code;
				const { message, result } = data.response;

				if (code >= 300) {
					logMessage += `\x1B[31m - ${code} ${message} \x1B[m`;
				} else {
					logMessage += `\x1B[32m - ${code} ${ message }\x1B[m`;
				}

				if (result) {
					logMessage += ' data:';
					console.log(logMessage, result );
					return
				}
				console.log(logMessage);
			} else {
				if ('POST' === method) {
					console.log(logMessage, data.body)
				} else {
					console.log(logMessage)
				}
			}

			return;
		}

		// Log PAGE_VIEW events
		if ('PAGE_VIEW' === event) {
			logMessage += `${data.url}`
			console.log(logMessage);
			return;
		}

		// default case
		console.log(logMessage, data);
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
		return true;
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
		eventSource: () => evtSrc
	}
};