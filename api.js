// API functions for Campus Connect Portal

// Fetch events from API
async function fetchEvents() {
    try {
        const response = await fetch('api/events.php');
        if (!response.ok) {
            throw new Error('Failed to fetch events');
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching events:', error);
        // Fallback to static data if API fails
        return eventsData || [];
    }
}

// Fetch clubs from API
async function fetchClubs() {
    try {
        const response = await fetch('api/clubs.php');
        if (!response.ok) {
            throw new Error('Failed to fetch clubs');
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching clubs:', error);
        // Fallback to static data if API fails
        return clubsData || [];
    }
}

// Fetch announcements from API
async function fetchAnnouncements() {
    try {
        const response = await fetch('api/announcements.php');
        if (!response.ok) {
            throw new Error('Failed to fetch announcements');
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching announcements:', error);
        // Fallback to static data if API fails
        return announcementsData || [];
    }
}

// Fetch event by ID
async function fetchEventById(id) {
    try {
        const response = await fetch(`api/event.php?id=${id}`);
        if (!response.ok) {
            throw new Error(`Failed to fetch event with ID ${id}`);
        }
        const event = await response.json();
        if (!event || event.error) {
            throw new Error(event.error || `Event with ID ${id} not found`);
        }
        return event;
    } catch (error) {
        console.error('Error fetching event by ID:', error);
        
        // Fallback to static data if API fails
        if (typeof eventsData !== "undefined") {
            return eventsData.find(event => event.id === id);
        }
        return null;
    }
}

// Fetch club by ID
async function fetchClubById(id) {
    try {
        const clubs = await fetchClubs();
        return clubs.find(club => club.id === id);
    } catch (error) {
        console.error('Error fetching club by ID:', error);
        return null;
    }
}

// Fetch announcement by ID
async function fetchAnnouncementById(id) {
    try {
        const announcements = await fetchAnnouncements();
        return announcements.find(announcement => announcement.id === id);
    } catch (error) {
        console.error('Error fetching announcement by ID:', error);
        return null;
    }
}