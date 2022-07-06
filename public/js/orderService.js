const burial = document.getElementById('burial');
const model = document.getElementById('model');
const extra = document.getElementById('extra');
const material = document.getElementById('material');
const color = document.getElementById('color');
const radiosModel = document.getElementsByClassName('radioModel');
const radiosColor = document.getElementsByClassName('radioColor');
const radiosMaterial = document.getElementsByClassName('radioMaterial');
const radiosExtra = document.getElementsByClassName('radioExtra');
const models = document.formOrderService.model;
const burials = document.formOrderService.burial;

const dependanceCharacteristics = {
    extra: extra,
    material: material,
    color: color,
};

/* Filter models based on burial selected */
Array.from(burials).forEach(item => {
    item.addEventListener('click', function (e) {

        //init
        const burialVersion = e.target.dataset.burialVersion; // get burial model
        model.classList.remove('hidden'); // show model container
        const modelItems = model.getElementsByClassName('modelItem');

        // filters model
        Array.from(modelItems).forEach((m) => {

            if (m.dataset.burialVersion === burialVersion) m.classList.remove('hidden');
            else m.classList.add('hidden');
            m.checked = false;
        });

        /* Set all radios to unchecked */
        [radiosMaterial, radiosExtra, radiosColor, radiosModel].forEach((radios) => {
            Array.from(radios).forEach((radio) => {
                radio.checked = false;
            });
        });

        /* Hide all container div */
        Object.values(dependanceCharacteristics).forEach((value) => {
            value.classList.add('hidden');
        });
    });
})

/* Filter others items based on model selected */
Array.from(models).forEach(item => {
    item.addEventListener('click', function (e) {

        const modelVersion = e.target.dataset.modelVersion;

        /* Hide all related item */
        Object.entries(dependanceCharacteristics).forEach(([key, value]) => {

            value.classList.remove('hidden');
            const items = value.getElementsByClassName(key + 'Item');

            Array.from(items).forEach(i => {
                if (i.hasAttribute('data-model-version')) {
                    if (i.dataset.modelVersion === modelVersion) i.classList.remove('hidden');
                    else i.classList.add('hidden');
                } else i.classList.remove('hidden');

            });

            /* Set all radios to unchecked */
            [radiosExtra, radiosMaterial, radiosColor].forEach((radios) => {
                console.log(radios);
                Array.from(radios).forEach((radio) => {
                    radio.checked = false;
                });
            });
        });
    });
});