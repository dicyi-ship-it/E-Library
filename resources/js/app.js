import './bootstrap';

const attendanceScanner = document.querySelector('[data-qr-attendance]');

if (attendanceScanner) {
    const startButton = attendanceScanner.querySelector('[data-qr-start]');
    const stopButton = attendanceScanner.querySelector('[data-qr-stop]');
    const video = attendanceScanner.querySelector('[data-qr-video]');
    const placeholder = attendanceScanner.querySelector('[data-qr-placeholder]');
    const status = attendanceScanner.querySelector('[data-qr-status]');
    const identityInput = attendanceScanner.querySelector('[data-qr-identity]');
    const sourceInput = attendanceScanner.querySelector('[data-qr-source]');

    let stream = null;
    let detector = null;
    let scanning = false;

    const setStatus = (message) => {
        status.textContent = message;
    };

    const stopScanner = () => {
        scanning = false;

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }

        video.pause();
        video.srcObject = null;
        video.classList.add('hidden');
        placeholder.classList.remove('hidden');
    };

    const scanFrame = async () => {
        if (! scanning || ! detector || video.readyState < HTMLMediaElement.HAVE_ENOUGH_DATA) {
            if (scanning) {
                requestAnimationFrame(scanFrame);
            }

            return;
        }

        try {
            const codes = await detector.detect(video);

            if (codes.length > 0) {
                identityInput.value = codes[0].rawValue.trim();
                sourceInput.value = 'qr';
                setStatus('QR terbaca. Periksa nomor induk, lalu tekan Catat Kehadiran.');
                stopScanner();
                identityInput.focus();
                return;
            }
        } catch (error) {
            setStatus('Scanner belum bisa membaca QR. Pastikan kamera mengarah jelas ke kode.');
        }

        requestAnimationFrame(scanFrame);
    };

    startButton?.addEventListener('click', async () => {
        if (! ('BarcodeDetector' in window) || ! navigator.mediaDevices?.getUserMedia) {
            setStatus('Browser ini belum mendukung scan QR langsung. Silakan isi nomor induk secara manual.');
            identityInput.focus();
            return;
        }

        try {
            const supportedFormats = await BarcodeDetector.getSupportedFormats();

            if (! supportedFormats.includes('qr_code')) {
                setStatus('Browser ini belum mendukung format QR. Silakan isi nomor induk secara manual.');
                identityInput.focus();
                return;
            }

            detector = new BarcodeDetector({ formats: ['qr_code'] });
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' },
                audio: false,
            });

            video.srcObject = stream;
            video.classList.remove('hidden');
            placeholder.classList.add('hidden');
            await video.play();

            scanning = true;
            sourceInput.value = 'qr';
            setStatus('Scanner aktif. Arahkan kamera ke QR nomor induk.');
            requestAnimationFrame(scanFrame);
        } catch (error) {
            stopScanner();
            setStatus('Kamera tidak dapat diakses. Izinkan kamera atau gunakan input manual.');
            identityInput.focus();
        }
    });

    stopButton?.addEventListener('click', () => {
        stopScanner();
        sourceInput.value = 'manual';
        setStatus('Scanner berhenti. Anda tetap bisa mengisi nomor induk secara manual.');
    });
}

const kioskClocks = document.querySelectorAll('[data-kiosk-clock]');

if (kioskClocks.length) {
    const updateClock = () => {
        const time = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        }).format(new Date()).replace('.', ':');

        kioskClocks.forEach((clock) => {
            clock.textContent = time;
        });
    };

    updateClock();
    setInterval(updateClock, 1000);
}

const kioskTabs = document.querySelectorAll('[data-kiosk-tab]');
const kioskPanels = document.querySelectorAll('[data-kiosk-panel]');

if (kioskTabs.length && kioskPanels.length) {
    const showPanel = (target) => {
        kioskTabs.forEach((tab) => {
            tab.classList.toggle('kiosk-tab-active', tab.dataset.kioskTab === target);
        });

        kioskPanels.forEach((panel) => {
            const isActive = panel.dataset.kioskPanel === target;
            panel.classList.toggle('hidden', ! isActive);
            panel.classList.toggle('grid', isActive);
        });

        const firstInput = document.querySelector(`[data-kiosk-panel="${target}"] input:not([type="hidden"]), [data-kiosk-panel="${target}"] select`);
        firstInput?.focus();
    };

    kioskTabs.forEach((tab) => {
        tab.addEventListener('click', () => showPanel(tab.dataset.kioskTab));
    });

    showPanel(document.body.dataset.kioskDefaultTab || 'checkin');
}

document.querySelectorAll('[data-searchable-select]').forEach((select) => {
    const input = select.querySelector('[data-searchable-input]');
    const valueInput = select.querySelector('[data-searchable-value]');
    const optionsBox = select.querySelector('[data-searchable-options]');
    const hint = select.querySelector('[data-searchable-hint]');
    const options = Array.from(select.querySelectorAll('[data-searchable-option]'));
    let activeIndex = -1;

    const visibleOptions = () => options.filter((option) => ! option.classList.contains('hidden'));

    const open = () => {
        optionsBox.classList.remove('hidden');
    };

    const close = () => {
        optionsBox.classList.add('hidden');
        activeIndex = -1;
        options.forEach((option) => option.classList.remove('searchable-option-active'));
    };

    const setActive = (index) => {
        const visible = visibleOptions();

        options.forEach((option) => option.classList.remove('searchable-option-active'));

        if (! visible.length) {
            activeIndex = -1;
            return;
        }

        activeIndex = Math.max(0, Math.min(index, visible.length - 1));
        visible[activeIndex].classList.add('searchable-option-active');
        visible[activeIndex].scrollIntoView({ block: 'nearest' });
    };

    const selectOption = (option) => {
        valueInput.value = option.dataset.value;
        input.value = option.dataset.label;
        input.dataset.selectedLabel = option.dataset.label;
        close();
    };

    const filter = () => {
        const query = input.value.trim().toLowerCase();
        let visibleCount = 0;

        if (input.value !== input.dataset.selectedLabel) {
            valueInput.value = '';
        }

        options.forEach((option) => {
            const isVisible = ! query || option.dataset.search.includes(query) || option.dataset.label.toLowerCase().includes(query);
            option.classList.toggle('hidden', ! isVisible);

            if (isVisible) {
                visibleCount += 1;
            }
        });

        hint.textContent = visibleCount
            ? `${visibleCount} hasil ditemukan. Pilih salah satu dari daftar.`
            : 'Tidak ada hasil. Coba kata kunci lain.';

        open();
        setActive(visibleCount ? 0 : -1);
    };

    optionsBox.classList.add('hidden');

    if (input.value) {
        input.dataset.selectedLabel = input.value;
    }

    input.addEventListener('focus', filter);
    input.addEventListener('input', filter);

    input.addEventListener('keydown', (event) => {
        const visible = visibleOptions();

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            open();
            setActive(activeIndex + 1);
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            open();
            setActive(activeIndex <= 0 ? visible.length - 1 : activeIndex - 1);
        }

        if (event.key === 'Enter' && activeIndex >= 0 && visible[activeIndex]) {
            event.preventDefault();
            selectOption(visible[activeIndex]);
        }

        if (event.key === 'Escape') {
            close();
        }
    });

    options.forEach((option) => {
        option.addEventListener('click', () => selectOption(option));
    });

    document.addEventListener('click', (event) => {
        if (! select.contains(event.target)) {
            close();
        }
    });

    select.closest('form')?.addEventListener('submit', (event) => {
        if (! valueInput.value) {
            event.preventDefault();
            open();
            filter();
            input.focus();
            hint.textContent = 'Pilih data dari daftar sebelum menyimpan.';
        }
    });
});
