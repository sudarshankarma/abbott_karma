// Update unread count periodically
function updateUnreadCount() {
    fetch('?controller=support&action=getUnreadCount')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('unreadSupportCount');
            if (badge && data.unread_count > 0) {
                badge.textContent = data.unread_count;
                badge.style.display = 'inline-block';
            } else if (badge) {
                badge.style.display = 'none';
            }
        });
}

// Update every 30 seconds
setInterval(updateUnreadCount, 30000);
document.addEventListener('DOMContentLoaded', updateUnreadCount);