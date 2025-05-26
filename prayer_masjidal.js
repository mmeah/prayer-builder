document.addEventListener('DOMContentLoaded', () => {
    const MASJID_ID = '1QL0MDAZ';
    const API_URL = `https://masjidal.com/api/v1/time/range?masjid_id=${MASJID_ID}`;

    const currentTimeElement = document.getElementById('current-time');
    const currentDateElement = document.getElementById('current-date');
    const prayerTimesBodyElement = document.getElementById('prayer-times-body');

    // Function to update the clock
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        if (currentTimeElement) {
            currentTimeElement.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }

    // Function to format time from API (e.g., "3:22AM" -> "03:22 AM")
    function formatApiTime(timeStr) {
        if (!timeStr || typeof timeStr !== 'string') return '--:--';
        const parts = timeStr.match(/(\d+):(\d+)(AM|PM)/i);
        if (!parts) return timeStr; // Return original if format is unexpected

        let hours = parseInt(parts[1], 10);
        const minutes = parts[2];
        const ampm = parts[3].toUpperCase();

        // Convert to 24-hour for potential sorting or comparison if needed, then back to 12-hour for display
        // For display, we just need to ensure two digits for hours if desired by a style
        // For this implementation, we'll assume API gives a good display format but ensure leading zero for consistency if needed.
        // The API format "3:22AM" is fine as is, but "03:22 AM" might look more uniform on a digital display.
        // Let's reformat slightly for leading zero on hour and space before AM/PM.
        // hours = String(hours).padStart(2, '0'); // This would make 3:22AM into 03:22AM
        // For now, directly use API format as it's clear. If more specific formatting is needed, it can be done here.
        // Simple reformat for consistency:
        return `${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
    }
    
    // Function to fetch and display prayer times
    async function fetchPrayerTimes() {
        try {
            const response = await fetch(API_URL);
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`);
            }
            const data = await response.json();

            if (data.status === 'success' && data.data) {
                const todaySalah = data.data.salah && data.data.salah[0] ? data.data.salah[0] : {};
                const todayIqamah = data.data.iqamah && data.data.iqamah[0] ? data.data.iqamah[0] : {};

                // Update date display
                if (currentDateElement) {
                    let dateText = todaySalah.date || 'Date not available';
                    if (todaySalah.hijri_date && todaySalah.hijri_month) {
                        dateText += ` (${todaySalah.hijri_date} ${todaySalah.hijri_month} ${todaySalah.hijri_year || ''})`;
                    }
                    currentDateElement.textContent = dateText.trim();
                }

                // Clear previous prayer times
                if (prayerTimesBodyElement) prayerTimesBodyElement.innerHTML = ''; 

                // Define prayers to display - including Jummah which might be only in iqamah
                const prayers = [
                    { name: 'Fajr', adhan: todaySalah.fajr, iqamah: todayIqamah.fajr },
                    { name: 'Sunrise', adhan: todaySalah.sunrise, iqamah: null }, // No Iqamah for Sunrise typically
                    { name: 'Dhuhr', adhan: todaySalah.zuhr, iqamah: todayIqamah.zuhr },
                    { name: 'Asr', adhan: todaySalah.asr, iqamah: todayIqamah.asr },
                    { name: 'Maghrib', adhan: todaySalah.maghrib, iqamah: todayIqamah.maghrib },
                    { name: 'Isha', adhan: todaySalah.isha, iqamah: todayIqamah.isha }
                ];
                
                // Handle Jummah - it might replace Dhuhr on Fridays, or be listed separately
                // The API provides jummah1, jummah2 in iqamah section.
                // For simplicity, we'll add Jummah times if present.
                // A more sophisticated approach would be to check if it's Friday.
                if (todayIqamah.jummah1) {
                     prayers.push({ name: 'Jummah 1', adhan: todayIqamah.jummah1, iqamah: todayIqamah.jummah1 }); // Often Adhan time is same as Iqamah for Jummah from such APIs
                }
                if (todayIqamah.jummah2) {
                     prayers.push({ name: 'Jummah 2', adhan: todayIqamah.jummah2, iqamah: todayIqamah.jummah2 });
                }


                prayers.forEach(prayer => {
                    if (!prayerTimesBodyElement) return;
                    const row = prayerTimesBodyElement.insertRow();
                    row.insertCell().textContent = prayer.name;
                    row.insertCell().textContent = prayer.adhan ? formatApiTime(prayer.adhan) : '--:--';
                    row.insertCell().textContent = prayer.iqamah ? formatApiTime(prayer.iqamah) : (prayer.name === 'Sunrise' ? '' : '--:--');
                });

            } else {
                throw new Error(data.message || 'Failed to retrieve prayer times.');
            }
        } catch (error) {
            console.error('Error fetching prayer times:', error);
            if (currentDateElement) currentDateElement.textContent = 'Could not load prayer times.';
            if (prayerTimesBodyElement) {
                prayerTimesBodyElement.innerHTML = '<tr><td colspan="3">Error loading times.</td></tr>';
            }
        }
    }

    // Initial setup
    updateClock(); // Update clock immediately
    setInterval(updateClock, 1000); // Update clock every second
    fetchPrayerTimes(); // Fetch prayer times on load

    // Optional: Refresh prayer times periodically (e.g., every hour)
    // setInterval(fetchPrayerTimes, 3600000); // 3600000ms = 1 hour
});
