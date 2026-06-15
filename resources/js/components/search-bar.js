export function searchBar() {
    return {
        query: '',
        barang: [],
        transaksi: [],
        open: false,
        loading: false,
        activeItem: null,
        flatItems: [],
        timer: null,
        tipeLabel: {
            'barang_masuk': 'Barang Masuk',
            'barang_keluar': 'Barang Keluar',
            'mutasi': 'Mutasi'
        },
        hasResults() {
            return this.barang.length > 0 || this.transaksi.length > 0;
        },
        buildFlat() {
            const b = this.barang.map(i => ({...i, _type: 'barang'}));
            const t = this.transaksi.map(i => ({...i, _type: 'transaksi'}));
            this.flatItems = [...b, ...t];
        },
        moveDown() {
            this.buildFlat();
            if (!this.flatItems.length) return;
            const idx = this.flatItems.indexOf(this.activeItem);
            this.activeItem = this.flatItems[Math.min(idx + 1, this.flatItems.length - 1)];
        },
        moveUp() {
            this.buildFlat();
            if (!this.flatItems.length) return;
            const idx = this.flatItems.indexOf(this.activeItem);
            this.activeItem = this.flatItems[Math.max(idx - 1, 0)];
        },
        search() {
            this.activeItem = null;
            clearTimeout(this.timer);
            if (!this.query || this.query.length < 2) {
                this.barang = [];
                this.transaksi = [];
                this.open = false;
                return;
            }
            this.timer = setTimeout(() => {
                this.loading = true;
                fetch(window.__gudangjateng.searchUrl + '?q=' + encodeURIComponent(this.query))
                    .then(r => r.json())
                    .then(data => {
                        this.barang = data.barang || [];
                        this.transaksi = data.transaksi || [];
                        this.open = this.hasResults();
                        this.loading = false;
                    })
                    .catch(() => {
                        this.barang = [];
                        this.transaksi = [];
                        this.open = false;
                        this.loading = false;
                    });
            }, 300);
        },
        goTo(item) {
            if (item._type === 'barang') {
                this.goToBarang(item);
            } else {
                this.goToTransaksi(item);
            }
        },
        goToBarang(item) {
            this.open = false;
            this.query = '';
            window.location.href = window.__gudangjateng.kartuStokUrl + '?barang_id=' + item.id;
        },
        goToTransaksi(item) {
            this.open = false;
            this.query = '';
            const urlTemplate = window.__gudangjateng?.transaksiDetailUrls?.[item.tipe];
            if (urlTemplate) {
                window.location.href = urlTemplate.replace('__ID__', item.id);
            }
        }
    };
}
