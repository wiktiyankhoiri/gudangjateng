export function notificationBell() {
    return {
        dropdownOpen: false,
        notifications: [],
        unreadCount: 0,
        notifying: false,
        loading: false,
        fetchNotifications() {
            this.loading = true;
            fetch(window.__gudangjateng.notificationsListUrl)
                .then(r => r.json())
                .then(data => {
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.unread_count || 0;
                    this.notifying = this.unreadCount > 0;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },
        markAllRead() {
            fetch(window.__gudangjateng.notificationsReadAllUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.__gudangjateng.csrfToken,
                        'Accept': 'application/json',
                    }
                })
                .then(() => {
                    this.notifications.forEach(n => n.is_read = '1');
                    this.unreadCount = 0;
                    this.notifying = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },
        markRead(id, refId, type) {
            fetch(window.__gudangjateng.notificationsMarkReadUrl.replace('__ID__', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.__gudangjateng.csrfToken,
                        'Accept': 'application/json',
                    }
                })
                .then(() => {
                    const n = this.notifications.find(x => x.id == id);
                    if (n) {
                        n.is_read = '1';
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                    if (this.unreadCount === 0) this.notifying = false;
                    window.location.href = window.__gudangjateng.notificationsAllUrl + '?highlight=' + id;
                })
                .catch(() => {
                    this.loading = false;
                });
        },
        typeLabel(type) {
            const map = {
                'barang_masuk': 'Barang Masuk',
                'barang_keluar': 'Barang Keluar',
                'mutasi': 'Mutasi',
                'penyesuaian_stok': 'Penyesuaian',
                'initialstok': 'Initial Stok'
            };
            return map[type] || type;
        },
        timeAgo(d) {
            const s = Math.floor((Date.now() - new Date(d)) / 1000);
            if (s < 60) return 'Baru saja';
            if (s < 3600) return Math.floor(s / 60) + ' menit lalu';
            if (s < 86400) return Math.floor(s / 3600) + ' jam lalu';
            return Math.floor(s / 86400) + ' hari lalu';
        }
    };
}
