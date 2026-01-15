jQuery(function ($) {

    /* ---------------------------
       OPEN MODAL
    --------------------------- */
    $(document).on('click', '.furniplace-ai-btn', function (e) {
        e.preventDefault();
        $('#furniplace-modal').fadeIn();
    });

    /* ---------------------------
       CLOSE MODAL
    --------------------------- */
    $(document).on('click', '#furniplace-close', function (e) {
        e.preventDefault();
        $('#furniplace-modal').fadeOut();
    });

    const FurniPlaceDB = {
        db: null,

        init() {
            if (this.db) return Promise.resolve();

            return new Promise((resolve, reject) => {
                const req = indexedDB.open('furniplace_ai', 1);

                req.onupgradeneeded = e => {
                    const db = e.target.result;
                    if (!db.objectStoreNames.contains('images')) {
                        db.createObjectStore('images', { keyPath: 'id' });
                    }
                };

                req.onsuccess = e => {
                    this.db = e.target.result;
                    resolve();
                };

                req.onerror = () => reject('IndexedDB init failed');
            });
        },

        async save(productId, blob) {
            await this.init();

            return new Promise(resolve => {
                const tx = this.db.transaction('images', 'readwrite');
                const store = tx.objectStore('images');

                // Save new image
                store.put({
                    id: `${productId}_${Date.now()}`,
                    productId,
                    blob,
                    time: Date.now()
                });

                // After write completes, cleanup
                tx.oncomplete = async () => {
                    const all = await this.get(productId);

                    // keep only latest 3
                    all.slice(3).forEach(item => {
                        const delTx = this.db.transaction('images', 'readwrite');
                        delTx.objectStore('images').delete(item.id);
                    });

                    resolve();
                };
            });
        },

        async get(productId) {
            await this.init();

            return new Promise(resolve => {
                const tx = this.db.transaction('images', 'readwrite');
                const store = tx.objectStore('images');
                const req = store.getAll();

                req.onsuccess = () => {
                    const items = req.result
                        .filter(i => i.productId == productId)
                        .sort((a, b) => b.time - a.time);

                    // HARD ENFORCE: keep only latest 3
                    items.slice(3).forEach(item => store.delete(item.id));

                    resolve(items.slice(0, 3));
                };
            });
        }

    };

    /* ======================================================
       BASE64 → BLOB
    ====================================================== */
    function furniplaceBase64ToBlob(base64) {
        const parts = base64.split(',');
        const mime = parts[0].match(/:(.*?);/)[1];
        const bin = atob(parts[1]);
        const len = bin.length;
        const arr = new Uint8Array(len);

        for (let i = 0; i < len; i++) {
            arr[i] = bin.charCodeAt(i);
        }

        return new Blob([arr], { type: mime });
    }

    /* ======================================================
       SAVE IMAGE (LAST 3 PER PRODUCT)
    ====================================================== */
    async function furniplaceSaveImage(productId, base64Image) {
        const blob = furniplaceBase64ToBlob(base64Image);
        await FurniPlaceDB.save(productId, blob);
    }

    /* ======================================================
       RENDER IMAGE-BASED TABS HISTORY
    ====================================================== */
    async function furniplaceRenderHistory(productId) {
        const items = await FurniPlaceDB.get(productId);
        const $wrap = $('#furniplace-history-tabs');

        if (!items.length) {
            $wrap.empty();
            return;
        }

        const tabsTpl = document.getElementById('furniplace-history-tabs-template');
        const triggerTpl = document.getElementById('furniplace-history-trigger-template');
        const panelTpl = document.getElementById('furniplace-history-panel-template');

        if (!tabsTpl || !triggerTpl || !panelTpl) return;

        const tabs = tabsTpl.content.cloneNode(true);
        const tabList = tabs.querySelector('tab-list');
        const tabContent = tabs.querySelector('tab-content');

        items.forEach(item => {
            const url = URL.createObjectURL(item.blob);

            // Thumbnail tab
            const triggerNode = triggerTpl.content.cloneNode(true);
            triggerNode.querySelector('img').src = url;
            triggerNode.querySelector('img').loading = 'lazy';
            tabList.appendChild(triggerNode);

            // Full image panel
            const panelNode = panelTpl.content.cloneNode(true);
            panelNode.querySelector('img').src = url;
            tabContent.appendChild(panelNode);
        });

        $wrap.empty().append(tabs);
    }

    /* ======================================================
       MODAL OPEN / CLOSE
    ====================================================== */
    $(document).on('click', '.furniplace-ai-btn', function (e) {
        e.preventDefault();
        $('#furniplace-modal').fadeIn();
        furniplaceRenderHistory(FurniPlaceAI.product_id);
    });

    $(document).on('click', '#furniplace-close', function (e) {
        e.preventDefault();
        $('#furniplace-modal').fadeOut();
    });

    /* ======================================================
       GENERATE IMAGE (AJAX)
    ====================================================== */
    $(document).on('click', '#furniplace-generate', function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('furniplace-room');
        if (!fileInput || !fileInput.files.length) {
            alert('Please upload room image');
            return;
        }

        const file = fileInput.files[0];
        const instruction = $('#furniplace-instruction').val();
        const reader = new FileReader();

        $('#furniplace-result').empty();
        $('#furniplace-loading').fadeIn();
        $('#furniplace-generate').prop('disabled', true).text('Generating...');

        reader.onload = function (e) {
            const base64 = e.target.result.split(',')[1];

            $.post(FurniPlaceAI.ajax_url, {
                action: 'furniplace_generate',
                product_id: FurniPlaceAI.product_id,
                roomBase64: base64,
                roomMime: file.type,
                instruction: instruction
            })
                .done(async function (res) {
                    if (!res || !res.success) {
                        alert(res?.data || 'AI generation failed');
                        return;
                    }

                    $('#furniplace-result').html(`<img src="${res.data}">`);

                    await furniplaceSaveImage(FurniPlaceAI.product_id, res.data);
                    await furniplaceRenderHistory(FurniPlaceAI.product_id);
                })
                .fail(() => alert('Server error'))
                .always(() => {
                    $('#furniplace-loading').fadeOut();
                    $('#furniplace-generate').prop('disabled', false).text('Generate Preview');
                });
        };

        reader.readAsDataURL(file);
    });

    furniplaceRenderHistory(FurniPlaceAI.product_id);
});