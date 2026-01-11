/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import "./echo";
import '../images/icons/perwira_logo.png';
import '../images/favicon.jpg';

Echo.private(`App.Models.User.${window.userId}`).notification(
    (notification) => {
        Livewire.dispatch("notificationReceived", {notification: notification});
    }
);
