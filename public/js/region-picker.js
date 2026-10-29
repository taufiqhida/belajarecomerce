/**
 * Pemilih wilayah Indonesia untuk alamat pengiriman.
 * Data dari /api/wilayah (proxy + cache). Kalau gagal dimuat, pembeli tetap bisa
 * mengisi alamat lengkap secara manual di kolom detail.
 *
 *   RegionPicker.init('cart', { zoneSelectId: 'cartShippingZone', onZone: cartUpdateCalc });
 *   RegionPicker.address('cart', detailText)  // string alamat gabungan, atau null kalau pilihan belum lengkap
 */
window.RegionPicker = (function () {
    const levels = ['Prov', 'Kab', 'Kec', 'Desa'];
    const urls = {
        Prov: () => '/api/wilayah/provinces',
        Kab: id => '/api/wilayah/regencies/' + id,
        Kec: id => '/api/wilayah/districts/' + id,
        Desa: id => '/api/wilayah/villages/' + id,
    };
    const state = {};

    const el = (p, l) => document.getElementById(p + l);

    function fill(p, level, rows) {
        const sel = el(p, level);
        const label = { Prov: 'provinsi', Kab: 'kabupaten/kota', Kec: 'kecamatan', Desa: 'desa/kelurahan' }[level];
        sel.innerHTML = '<option value="">-- Pilih ' + label + ' --</option>' +
            rows.map(r => '<option value="' + r.id + '" data-name="' + r.name.replace(/"/g, '&quot;') + '">' + r.name + '</option>').join('');
        sel.disabled = rows.length === 0;
    }

    function reset(p, fromLevel) {
        levels.slice(levels.indexOf(fromLevel)).forEach(l => {
            const sel = el(p, l);
            sel.innerHTML = '<option value="">-- Pilih --</option>';
            sel.disabled = true;
        });
    }

    async function load(p, level, parentId) {
        const sel = el(p, level);
        sel.disabled = true;
        sel.innerHTML = '<option value="">Memuat...</option>';
        try {
            const res = await fetch(urls[level](parentId), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('status ' + res.status);
            fill(p, level, await res.json());
        } catch (e) {
            // gagal: biarkan kosong, pembeli isi alamat manual
            sel.innerHTML = '<option value="">(tidak tersedia)</option>';
            sel.disabled = true;
            if (level === 'Prov') state[p].failed = true;   // tanpa kode wilayah, ongkir ditentukan admin
            const note = el(p, 'Note');
            if (note) note.style.display = 'block';
        }
    }

    const nameOf = (p, l) => {
        const o = el(p, l).selectedOptions[0];
        return o && o.value ? o.dataset.name : '';
    };

    const codeOf = (p, l) => { const o = el(p, l).selectedOptions[0]; return o && o.value ? o.value : ''; };

    /**
     * Zona ongkir dicocokkan otomatis dari kode wilayah (pembeli tidak memilihnya):
     * tarif kecamatan lebih diutamakan, lalu tarif flat kabupaten/kota. Server menghitung ulang
     * dengan cara yang sama. Tidak cocok = admin yang menentukan ongkir setelah pesanan masuk.
     */
    function pickZone(p) {
        const cfg = state[p];
        const zoneSel = cfg.zoneSelectId ? document.getElementById(cfg.zoneSelectId) : null;
        if (!zoneSel) return;
        const kec = codeOf(p, 'Kec'), kab = codeOf(p, 'Kab');
        const zones = Array.from(zoneSel.options).filter(o => o.value);
        let hit = null;
        if (kec && kab) {
            hit = zones.find(o => o.dataset.district === kec)
                || zones.find(o => o.dataset.regency === kab && !o.dataset.district)
                || null;
        }
        cfg.kecChosen = !!kec;
        cfg.matched = !!hit;
        zoneSel.value = hit ? hit.value : '';
        const info = el(p, 'ZoneInfo');
        if (info) {
            if (!kec) { info.style.display = 'none'; }
            else {
                info.style.display = 'block';
                if (hit) {
                    info.style.color = '#15803d';
                    info.textContent = '🚚 Ongkir ke ' + hit.dataset.name.split(',').pop().trim() + ': Rp ' + Math.round(parseFloat(hit.dataset.fee || 0)).toLocaleString('id');
                } else {
                    info.style.color = '#b45309';
                    info.textContent = '🚚 Ongkir ke daerah ini belum ada tarif otomatis. Pesanan tetap bisa dikirim: admin akan menghubungi Anda untuk ongkir dan mengirim invoice.';
                }
            }
        }
        if (cfg.onZone) cfg.onZone();
    }
    function init(p, cfg) {
        state[p] = Object.assign({ failed: false }, cfg || {});
        el(p, 'Prov').addEventListener('change', e => {
            reset(p, 'Kab');
            pickZone(p);
            if (e.target.value) load(p, 'Kab', e.target.value);
        });
        el(p, 'Kab').addEventListener('change', e => {
            reset(p, 'Kec');
            pickZone(p);
            if (e.target.value) load(p, 'Kec', e.target.value);
        });
        el(p, 'Kec').addEventListener('change', e => {
            reset(p, 'Desa');
            pickZone(p);
            if (e.target.value) load(p, 'Desa', e.target.value);
        });
        reset(p, 'Kab');
        load(p, 'Prov');
    }

    /**
     * Alamat gabungan: "detail, Desa X, Kec. Y, Kabupaten Z, Provinsi".
     * Null kalau wilayah sudah tersedia tapi pembeli belum memilih sampai kecamatan.
     */
    function address(p, detail) {
        const cfg = state[p];
        detail = (detail || '').trim();
        if (!cfg || cfg.failed) return detail;   // API gagal: alamat manual saja
        if (!nameOf(p, 'Prov') || !nameOf(p, 'Kab') || !nameOf(p, 'Kec')) return null;
        const desaSel = el(p, 'Desa');
        const hasDesa = desaSel.options.length > 1;
        if (hasDesa && !nameOf(p, 'Desa')) return null;
        return [detail, nameOf(p, 'Desa') && 'Desa/Kel. ' + nameOf(p, 'Desa'), 'Kec. ' + nameOf(p, 'Kec'), nameOf(p, 'Kab'), nameOf(p, 'Prov')]
            .filter(Boolean).join(', ');
    }

    // Kode wilayah terpilih untuk dikirim ke server
    const codes = p => ({ regency: codeOf(p, 'Kab') || null, district: codeOf(p, 'Kec') || null });

    // true kalau ongkir belum bisa dihitung otomatis (daerah tanpa tarif, atau daftar wilayah gagal dimuat)
    const needsAdmin = p => !!state[p] && (state[p].failed || (state[p].kecChosen && !state[p].matched));

    return { init, address, codes, needsAdmin };
})();
