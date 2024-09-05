// Déposer un fichier
let fileInput = null;
if (typeof canImportPdf === 'undefined') {
    canImportPdf = false;
}


let affectionChampOffreFormulaire = [];

if(typeof  documentFieldsForm === 'undefined'){
    documentFieldsForm = [];
}

const positionChampTemplate = [];

let gridDocument = {x: 50, y: 60}

$('html').on("dragover", function (e) {
    e.preventDefault();
    e.stopPropagation();
});

let svgNode  = new BARCode({
    msg :  "A234567891"
    ,dim : [ 220, 50]
    ,pal : ["#000000"]
    ,pad : [ 0, 0]
});

$('#btn-start-template-type').click(function () {
    window.location.href = 'index.php?p=template&o=ajouter&e=2&t=' + $('#type-template').val()
})

$("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });

// Fichier survole la box
$('.box-input-file').on('dragover', function () {
    // $('.title-drop').text('DEPOSER')
    $(this).addClass('box-input-file-leave');
});

// Fichier ne survole plus box
$('.box-input-file').on('dragleave', function () {
    // $('.title-drop').text('DEPOSER')
    $(this).removeClass('box-input-file-leave');
});

// Fichier déposer
$('.box-input-file').on('drop', function (e) {
    let inputFile = document.getElementById('uploadFile');

    inputFile.files = e.originalEvent.dataTransfer.files;

    fileInput = $('#uploadFile').prop('files')[0];

    fileDropped(fileInput)
});

$('#uploadFile').change(function () {
    if($(this).val() !== ''){
        fileInput = $('#uploadFile').prop('files')[0];
        fileDropped(fileInput)
    }
});

$('#save-template').click(function () {
    execScript($(this), importPdfLink);
});


// Fontion a executer lors de l'uploead du fichier
function fileDropped(file){
    if(file.name.length > 0){
        $('#filename-upload-template').html(file.name);
        $('#filesize-upload-template').html(FileConvertSize(file.size));
        $('#wrapper-template-file-pre-uploaded').show();
    }
    else{
        $('#filename-upload-template').html('');
        $('#filesize-upload-template').html(FileConvertSize(''));
        $('#wrapper-template-file-pre-uploaded').hide();
    }
    // $('.container-input-file').hide();
    //$('#form-upload-file').submit();
}

// Ouvre la popup
$('#btn-popup-remplir-formulaire').click(function () {
    $('#popup-remplir-formulaire-template').show()
})

// Partie importation du document
if(canImportPdf){
    affichageModeleDocument(importPdfLink)
}

async function affichageModeleDocument(pdfURL){
    let pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'library/pdfjs-2.4.456-dist/build/pdf.worker.js';
    let loadingTask;




    const container = $('#pdfViewer');

    loadingTask = pdfjsLib.getDocument(pdfURL);

    loadingTask.promise.then(function(pdf) {
        let nbPageToShow = pdf.numPages;



        container.empty();

        for(let pageNumber = 1; pageNumber <= nbPageToShow; pageNumber++){
            pdf.getPage(pageNumber).then(function(page) {
                renderPdf(pdfURL, page, container, pageNumber);
            });

            if(container.attr('data-action') === 'signature'){
                $('#select-page').append('<option value="'+pageNumber+'">page ' + pageNumber + '</option>');
            }
        }




        // Ajouter la partie de gauche d'un document (le bouton dignature et infos etc)
        $('#wrapper-container-signature').show();
        $('.btn-signer').addClass('btn-signer-modele');
        // $('.btn-signer').show();
        // $('#container-menu-edition').show();
        // $('#wrapper-container-input-annotation').show();
        // addInputAnnotation(); // Affiche le formulaire d'ajout de notification


    }, function (reason) {
        // PDF loading error
        // console.error(reason);
    });



}




async  function renderPdf(pdfURL, page, container, pageNum, annotationsPredefini = []){
    const { degrees, PDFDocument, rgb, StandardFonts } = PDFLib;
    let viewport = page.getViewport({scale: container.width() / page.getViewport({scale: 1}).width});
    // Prepare canvas using PDF page dimensions
    const canvasWrap = document.createElement('div');
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    const canvasViewer = document.createElement('canvas');
    canvasViewer.setAttribute('id', 'container-canvas')
    const contextViewer = canvasViewer.getContext('2d');
    canvasViewer.height = viewport.height;
    canvasViewer.width = viewport.width;

    page.getAnnotations().then(function(items) {
        documentFieldsForm = items;
        let nbTotalLigne = items.length;

        // if(items.length < champsOffre.length){
        //     nbTotalLigne = champsOffre.length;
        // }




        for(let i = 0; i < nbTotalLigne; i++){
            const containerBoxChamp = document.createElement('div')
            containerBoxChamp.className = 'box-champ-remplir-formulaire';

            const wrapperContainerChampOffre = document.createElement('div');
            wrapperContainerChampOffre.classList.add('wrapper-champ-formulaire-offre');
            wrapperContainerChampOffre.setAttribute('data-champ-id', champsOffre[i] ? champsOffre[i].id : '')




            const containerChampOffre = document.createElement('div')
            containerChampOffre.classList.add('champ-formulaire-offre');

            let selectElement = document.createElement('select');
            selectElement.classList.add('select-champs')

            const optionChamps = document.createElement('option');
            optionChamps.innerHTML = '(vide)';
            optionChamps.setAttribute('value', '0');
            selectElement.append(optionChamps);

            for(let j = 0; j < champsOffre.length; j++){
                const optionChamps = document.createElement('option');
                optionChamps.innerHTML = champsOffre[j].name;
                optionChamps.setAttribute('value', champsOffre[j].id);

                selectElement.append(optionChamps);
            }
            containerChampOffre.append(selectElement);
            // if(champsOffre[i]){
            //     containerChampOffre.classList.add('champ-formulaire-offre-valued');
            // }
            // containerChampOffre.classList.add('ui-widget-content');
            containerChampOffre.setAttribute('data-champ-id', champsOffre[i] ? champsOffre[i].id : '');

            // const champOffreSpan = document.createElement('span')
            // champOffreSpan.innerText = champsOffre[i] ? champsOffre[i].name : '';

            wrapperContainerChampOffre.append(containerChampOffre);
            // containerChampOffre.append(champOffreSpan);

            const containerArrow = document.createElement('div')
            containerArrow.className = 'box-arrow-remplir';
            containerArrow.innerHTML = items[i] ? '<i class="fas fa-arrow-right"></i>' : '';

            const containerChampTemplate = document.createElement('div')
            containerChampTemplate.className = items[i] ? 'champ-formulaire-template' : '';
            containerChampTemplate.setAttribute('data-input-id', items[i] ? i : null);

            const champTemplateSpan = document.createElement('span')
            champTemplateSpan.innerText = items[i] ? items[i].fieldName : '';

            containerChampTemplate.append(champTemplateSpan);

            containerBoxChamp.append(wrapperContainerChampOffre);
            containerBoxChamp.append(containerArrow);
            containerBoxChamp.append(containerChampTemplate);

            document.getElementById('container-remplir-formulaire').append(containerBoxChamp);
        }

        const champDraggable = $('.champ-formulaire-offre');
        const champDroppable = $('.wrapper-champ-formulaire-offre');

        // champDraggable.draggable({
        //     scroll: true,
        //     helper: "clone",
        //     revert: "invalid",
        //     containment: "#container-remplir-formulaire"
        // });
        //
        // champDraggable.droppable({
        //     accent: champDraggable,
        //     hoverClass: "ui-droppable-dragging",
        //     drop: function(event, ui){
        //         const champsId = parseInt(ui.draggable.attr('data-champs-id'));
        //         const parentDraggable = ui.draggable.parent();
        //
        //         // Ajout de l'element drag dans son nouveau emplacemment
        //         $(this).parent().append(ui.draggable);
        //
        //
        //         // Remplacement de l'element dans le parent de l'element drag
        //         parentDraggable.append($(this));
        //         // checkClassBordered();
        //
        //     }
        // })
    });



    canvasWrap.className = 'canvas-pdf-wrap';
    canvasWrap.setAttribute("data-page", pageNum.toString());




    canvasWrap.append(canvas);
    container.append(canvasWrap);
    container.append(canvasViewer);

    // Render PDF page into canvas context
    const renderContext = {
        canvasContext: context,
        viewport: viewport
    };
    const renderTask = page.render(renderContext);



    renderTask.promise.then(function () {
        drawGridAnnotationCss(canvasWrap, gridDocument.x, gridDocument.y);
    });

    const existingPdfBytes = await fetch(pdfURL).then(res => res.arrayBuffer());
    const pdfDoc = await PDFDocument.load(existingPdfBytes, {ignoreEncryption: true});
    const form = pdfDoc.getForm();


}


//Remplissage du formulaire dans le document
$('#btn-continuer-saved-annotation').click(function () {
    affectionChampOffreFormulaire = [];
    $('.box-champ-remplir-formulaire').each(function () {
        const champId = $(this).find('.select-champs').val();
        const inputId = $(this).find('.champ-formulaire-template').attr('data-input-id');

        if(inputId){
            affectionChampOffreFormulaire.push({
                champ_id: champId.length > 0 ? parseInt(champId) : 0,
                input_id: inputId.length > 0 ? parseInt(inputId) : null,
            });

            if(champId.length > 0 && inputId.length > 0){
                const elementChamp =$('.box-champ-to-add[data-champ-id="'+champId+'"]');
                const tooltipMessage =  elementChamp.find('.pattern-tooltip-message')
                elementChamp.attr('data-in-form', 1);
                elementChamp.attr('data-input-id', inputId);
                tooltipMessage.html('Placé dans formulaire (' + documentFieldsForm[inputId].fieldName + ')') ;
            }
        }
    });

    $('#popup-remplir-formulaire-template').hide();
    checkPlacedChamps()
});





function drawGridAnnotationCss(container, gridX = 50, gridY = 60){
    let containerGridAnnotation = document.createElement('div');
    containerGridAnnotation.className = 'container-grid-annotation';

    for(let y = gridY; y >= 0; y--){
        for(let x = 0; x <= gridX; x++){
            const elementBox = document.createElement('div');
            elementBox.classList.add('box-grid-droppable-hidden');
            elementBox.classList.add('box-grid-droppable');
            elementBox.setAttribute('data-x', x + 1)
            elementBox.setAttribute('data-y', y)
            // elementBox.innerText = y;
            containerGridAnnotation.append(elementBox);
        }
    }



    $(container).append(containerGridAnnotation);

    const gridRColumns= "repeat(" + (gridX + 1) + ", 1fr)";
    const heightCell = Math.round($(containerGridAnnotation).height() / gridY) ;
    const sizeBorder = 1;
    const gridRows= "repeat(" + gridY + ", " + (heightCell - ((sizeBorder * 2) * gridY)) +"px)";

    $(containerGridAnnotation).css({ "display": "grid",
        "gridTemplateColumns": gridRColumns,
        "gridTemplateRows": gridRows,
        'zIndex': 1999});

    let canDropELement = true; // Eviter les duplication
    $('.champ-draggable').draggable({
        scroll: true,
        helper: "clone",
        revert: "invalid",
        cancel: "button",
        draggingClass: 'champ-draggable-dragging',
        start: function (event, ui){
            canDropELement = true;
        }
    });


    let lastELementOvered = null;
    $('.box-grid-droppable').droppable({
        accept: ".champ-draggable, .champ-dropped",
        hoverClass: 'box-grid-droppable-hover',
        tolerance: "touch",
        over: function () {
            $('.box-grid-droppable-hover-cible').removeClass('box-grid-droppable-hover-cible');
            lastELementOvered = $('.box-grid-droppable-hover:eq(0)');
            lastELementOvered.addClass('box-grid-droppable-hover-cible');

            $('.champ-draggable').hasClass('box-grid-droppable-hover')
            // box-grid-droppable-hover:eq(0).fi
            // lastELementOvered
        },
        drop: function (event, ui){
            if(canDropELement){
                if(!ui.draggable.hasClass('champ-dropped')){
                    const boxChampDropped = document.createElement('div');
                    boxChampDropped.classList.add('champ-dropped');
                    boxChampDropped.classList.add('pattern-not-selectable');
                    boxChampDropped.setAttribute('data-champ-id', $(ui.draggable).closest('.box-champ-to-add').attr('data-champ-id'))
                    boxChampDropped.innerHTML = ui.draggable.html()
                    const btnDelete = document.createElement('div');
                    btnDelete.classList.add('btn-delete-to-pdf')
                    btnDelete.innerHTML = '<i class="fas fa-times"></i>'

                    const boxBtnDelete = document.createElement('div');
                    boxBtnDelete.classList.add('wrapper-btn-delete-to-pdf')

                    boxBtnDelete.append(btnDelete)
                    boxChampDropped.append(boxBtnDelete);
                    lastELementOvered.append(boxChampDropped)

                    $('.champ-dropped').draggable({
                        scroll: true,
                        revert: "invalid",
                        cancel: "button",
                        classes: {
                            "ui-draggable-dragging": "champ-draggable-dragging"
                        },
                        start: function (event, ui){
                            canDropELement = true;
                        }
                    });
                }
                else{

                }
                canDropELement = false;
                $('.box-grid-droppable-hover-cible').removeClass('box-grid-droppable-hover-cible');
                checkPlacedChamps();
            }
        }
    })
}

$(document).on('click', '.wrapper-btn-delete-to-pdf', function () {
    $(this).closest('.champ-dropped').remove();
});

// Affichage de la grille
$('#btn-show-grid').click(function () {
    const gridCss = $('.box-grid-droppable');
    gridCss.toggleClass('box-grid-droppable-hidden');
})

function checkPlacedChamps(){
    $('.box-champ-to-add').each(function () {
        const champId = parseInt($(this).attr('data-champ-id'));
        let hasPlaced = false;

        // On verifie si le champs a été ajouter dans le formulaire
        const checkValueFormulaire = affectionChampOffreFormulaire.find((o, i) =>{
            return o.champ_id === champId;
        })
        if(checkValueFormulaire){
            hasPlaced = true;
            $(this).find('.box-positionner-in-form').show();
        }

        // On verifie si il est plcé dans le docuyemnt directement
        const champDropped = $('.champ-dropped[data-champ-id="'+champId+'"]');
        if(champDropped.length > 0){
            hasPlaced = true;
            $(this).find('.box-positionner-in-file').show();
        }

        if(hasPlaced){
            $(this).addClass('box-champ-actif')
        }
        else{
            $(this).removeClass('box-champ-actif')
        }

    });
}

function execScript(elementTarget, fileURL, apercu =false){
    const file_data = fileURL;
    const request = new XMLHttpRequest();


    request.open('GET', fileURL, true);
    request.responseType = 'blob';
    request.onload = function () {
        const reader = new FileReader();

        reader.onload = function(e) {
            let data = e.target.result;

            const fileNamePredefini = 'test.pdf';

            // let tempValues = {code1_before: h_sha256Avant, code2_before: h_sha3_1Avant, user_id: user.id , filename: fileNamePredefini, filesize: 1};

            modifyPdf(data, 'test.pdf', user)
        };
        reader.readAsDataURL(request.response); // convert to base64 string
    };
    request.send();
}

async function modifyPdf(documentUrl, fileNameOrigin, user, resutl = []){
    const { degrees, PDFDocument, rgb, StandardFonts } = PDFLib;
    const url = documentUrl;
    const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer());
    const pdfDoc = await PDFDocument.load(existingPdfBytes, {ignoreEncryption: true});
    const var_pages = pdfDoc.getPages();
    const page = var_pages[0];
    const { width, height } = page.getSize();
    const helveticaFont = await pdfDoc.embedFont(StandardFonts.Helvetica);

    const form = pdfDoc.getForm();


    for(let i = 0; i < affectionChampOffreFormulaire.length; i++){

        inputDocu = form.getTextField(documentFieldsForm[affectionChampOffreFormulaire[i].input_id].fieldName);

        let checkValueFormulaire = champsOffre.find((o, d) =>{
            return o.id == affectionChampOffreFormulaire[i].champ_id;
        });


        if(checkValueFormulaire){
            inputDocu.setText(checkValueFormulaire.name);
        }
    }

    let checkValueFormulaire = champsOffre.find((o, i) =>{
        return o.champ_id === 8;
    })

    form.flatten();

    let svgNode  = new BARCode({
        msg :  "A234567891"
        ,dim : [ 220, 50]
        ,pal : ["#000000"]
        ,pad : [ 0, 0]
    });

    // On reset les champs du template
    sendAJAX('script', {type_id: type_id}, 'reset_champs', '#result-ajax');


    // On ajoute les elements placer directement dans le formulaire du PDF
    $('.box-champ-to-add[data-in-form="1"]').each(function () {
        const champId = $(this).attr('data-champ-id');
        let champSelected = champsOffre.find(cha => cha.id == champId);
        let inputId = $(this).attr('data-input-id');
        let inputSelected = documentFieldsForm[inputId]

        const values = {
            type_id: type_id,
            libelle: champSelected.name,
            description: '',
            bdd: champSelected.value,
            input_name: inputSelected.fieldName,
            posX: null,
            posY: null,
            page: 1,
        }
        sendAJAX('script', values, 'create_champs', '#result-ajax');
    })

    // On ajoute les elements placer dans la grille
    const tamponLargeur = 1; // le tampon
    const tamponHauteur = 1; // le tampon
    $('.champ-dropped').each(function () {
        const PosX = parseInt($(this).parent().attr('data-x'));
        const PosY = parseInt($(this).parent().attr('data-y'));
        const valueText = $(this).text();
        const champId = $(this).attr('data-champ-id');

        // let tamponX = ((width/gridDocument.x) * (PosX-1)) * (((width/gridDocument.x)-tamponLargeur)/2)
        // let tamponY = height - ((height/gridDocument.y) * (PosY - 1)) - (((height/gridDocument.y) - tamponHauteur) / 2);
        // let taillePolice = 14;
        //
        // while( helveticaFont.widthOfTextAtSize(valueText, taillePolice)> (0.7*tamponLargeur)){
        //     taillePolice = taillePolice-1;
        // }
        // let textLargeur = helveticaFont.widthOfTextAtSize(valueText, taillePolice);
        //
        // console
        //
        // page.drawText(valueText, {
        //     x: tamponX + Math.floor((tamponLargeur-textLargeur) / 2),
        //     y: tamponY - Math.floor((0.33 * tamponHauteur) / 2),
        //     size: taillePolice,
        //     font: helveticaFont
        // });
        //

        let champSelected = champsOffre.find(cha => cha.id == champId)

        const values = {
            type_id: type_id,
            libelle: champSelected.name,
            description: '',
            bdd: champSelected.value,
            input_name: '',
            posX: PosX,
            posY: PosY,
            page: 1,
        }

        sendAJAX('script', values, 'create_champs', '#result-ajax');

        let positionX = ((PosX / gridDocument.x) * width) - 11;
        if(PosX > 0){
            // positionX -= 4;
        }

        let positionY = ((PosY / gridDocument.y) * height);
        if(PosY > 0){
            positionY -= 5;
        }

        page.drawText(valueText, {
            x: positionX,
            y: positionY,
            size: 12,
            font: helveticaFont
        });
    });






    // CODE BARRE
    let svgText =svgNode.children[0].getAttribute('d');
    page.moveTo(231, 40);
    svgText = 'M1 0 V20 M5 0 V20 M7 0 V20 M8 0 V20 M9 0 V20 M11 0 V20 M12 0 V20 M13 0 V20 M15 0 V20 M17 0 V20 M18 0 V20 M19 0 V20 M21 0 V20 M25 0 V20 M27 0 V20 M29 0 V20 M30 0 V20 M31 0 V20 M33 0 V20 M35 0 V20 M36 0 V20 M37 0 V20 M41 0 V20 M43 0 V20 M45 0 V20 M46 0 V20 M47 0 V20 M49 0 V20 M51 0 V20 M55 0 V20 M56 0 V20 M57 0 V20 M59 0 V20 M60 0 V20 M61 0 V20 M63 0 V20 M65 0 V20 M67 0 V20 M71 0 V20 M72 0 V20 M73 0 V20 M75 0 V20 M76 0 V20 M77 0 V20 M79 0 V20 M81 0 V20 M82 0 V20 M83 0 V20 M85 0 V20 M89 0 V20 M91 0 V20 M92 0 V20 M93 0 V20 M95 0 V20 M97 0 V20 M98 0 V20 M99 0 V20 M101 0 V20 M105 0 V20 M107 0 V20 M108 0 V20 M109 0 V20 M111 0 V20 M113 0 V20 M114 0 V20 M115 0 V20 M117 0 V20 M121 0 V20 M122 0 V20 M123 0 V20 M125 0 V20 M127 0 V20 M129 0 V20 M130 0 V20 M131 0 V20 M133 0 V20 M137 0 V20 M138 0 V20 M139 0 V20 M141 0 V20 M143 0 V20 M145 0 V20 M147 0 V20 M148 0 V20 M149 0 V20 M153 0 V20 M154 0 V20 M155 0 V20 M157 0 V20 M159 0 V20 M161 0 V20 M162 0 V20 M163 0 V20 M165 0 V20 M169 0 V20 M170 0 V20 M171 0 V20 M173 0 V20 M175 0 V20 M177 0 V20 M181 0 V20 M182 0 V20 M183 0 V20 M185 0 V20 M187 0 V20 M188 0 V20 M189 0 V20 M191 0 V20 M193 0 V20 M197 0 V20 M198 0 V20 M199 0 V20 M201 0 V20 M203 0 V20 M204 0 V20 M205 0 V20 M207 0 V20 M209 0 V20 M213 0 V20 M214 0 V20 M215 0 V20 M217 0 V20 M219 0 V20 M220 0 V20 M221 0 V20 M223 0 V20 M225 0 V20 M229 0 V20 M231 0 V20 M232 0 V20 M233 0 V20 M235 0 V20 M236 0 V20 M237 0 V20 M239 0 V20 Z'
    page.drawSvgPath(svgText, { fillOpacity: 1, borderColor:  rgb(0, 0, 0), borderWidth: 0, scale: 0.55});

    // CODE CODE BARRE

    // page.moveTo(231, 30);
    // page.drawText(valueText, {
    //     x: positionX,
    //     y: positionY,
    //     size: 12,
    //     font: helveticaFont
    // });



    const pdfBytes = await pdfDoc.save();
    const pdfBlob = new Blob([pdfBytes], {type: 'application/pdf'});


    const newReader = new FileReader();


    newReader.onload = async function (e) {
        const newData = e.target.result
        download(pdfBytes, 'test.pdf', "application/pdf");
        // alert('Votre template a bien été sauvegardé')
    }
    newReader.readAsDataURL(pdfBlob);
}




/* Aperçu d'un template */
$(document).on('click', '#apercu-template', function () {
    execScript($(this), importPdfLink, true);
    window.open('index.php?p=offre&id=-1&a=1&to=' + type_id)
    // console.log('index.php?p=offre&id=-1&a=1&to=' + type_id)
})

