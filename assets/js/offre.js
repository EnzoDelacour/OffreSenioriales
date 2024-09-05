hasModifiyForm = false;
let prospect = null;
let lot = null;
let partenaire = null;
let resultProspect = null;
let resultPartenaire = null;
let resultLot = null;
let documentFieldsForm = [];
let gridDocumentOffre = {x: 50, y: 60}



if (typeof offreURL   === 'undefined') {
    offreURL   = undefined;
}

if (typeof page   === 'undefined') {
    page   = '';
}

if (typeof hasToGenerate  === 'undefined') {
    hasToGenerate  = false;
}

if (typeof champsOffre === 'undefined') {
    champsOffre = [];
}
if (typeof offre === 'undefined') {
    offre = [];
}

/* Changement de statut de l'offre */
$(document).on('click', '#btn-fiche-statut', function () {
    $('#box-select-statut').toggle()
})

$(document).on('click', '.btn-select-statut', function () {
    const statut_id = $(this).attr('data-statut-id')

    if(statut_id == 1){
        $('#box-status-actuel').html('<span data-statut-id="1" id="btn-fiche-statut" class="fiche-statut offre-en-cours" >En cours<i class="fas fa-pen"></i></span>')
    }
    else if(statut_id == 2){
        $('#box-status-actuel').html('<span data-statut-id="2" id="btn-fiche-statut" class="fiche-statut offre-valide" >Validé<i class="fas fa-pen"></i></span>')
    }
    else if(statut_id == 3){
        $('#box-status-actuel').html('<span data-statut-id="3" id="btn-fiche-statut" class="fiche-statut offre-expire" >Expiré<i class="fas fa-pen"></i></span>')
    }
    $('#box-select-statut').toggle();
})

/**
 * Le changement de destination modifie la liste de type d'offre disponible
 */
$('#destination').change(function () {
    const destinationId = $(this).val();
    const selectType = $('#type');
    const optionTypeNotCreer = $('#type').find('option:not([data-destination-id="0"])');

    optionTypeNotCreer.remove();
    // selectType.empty();

    const typeFiltered = offreType.filter(typeObj => (typeObj.typ_destination_id == destinationId || typeObj.typ_destination_id == '-1'));

    const optionType = document.createElement('option');
    optionType.setAttribute('value', 0);
    optionType.innerText = "--Selectionner type d'offre";
    selectType.append(optionType);

    for(let i = 0; i < typeFiltered.length; i++){
        const optionType = document.createElement('option');
        optionType.setAttribute('data-destination-id', typeFiltered[i].typ_destination_id);
        optionType.setAttribute('value', typeFiltered[i].typ_id);
        optionType.innerText = typeFiltered[i].typ_lib;
        selectType.append(optionType);
    }
    selectType.trigger('chosen:updated');
    setModifiedForm(true);

    // Affichage ou non du partenaire
    const inputPartenaireElement =  $('#container-partenaire-input input')

    if($('#destination').val() === '1'){
        inputPartenaireElement.attr('readonly', false);
        $('#container-partenaire-input button').show()
    }
    else{
       inputPartenaireElement.attr('readonly', true);
       inputPartenaireElement.val('')
        $('#container-partenaire-input button').hide()
    }
});


$(document).on('change', '.input-dependant', function () {
    let target = $(this).data('target');
    if(target){
        const element = $('.wrapper-element-form-hidden[data-name="'+target+'"]');
        element.slideDown();

        if($('#destination').val() === '1'){
            $('.wrapper-partenaire').show();
        }
        else{
            $('.wrapper-partenaire').hide();
            $('.wrapper-partenaire').val('')
        }

        if(element.find('#type')){
            element.find('#type').chosen()
        }
    }
})


$(document).on('input', '#prospect_id', function () {
    let target = $(this).data('target');
    if($(this).hasClass('input-dependant')){
        let error = true;
        let prospect_id = $(this).val();
        let messageElement = $('#result-search-prospect');
        messageElement.html('');

        if(prospect_id.length > 0){
            if(prospect_id.startsWith('P') || prospect_id.startsWith('p')){
                sendAJAX('script', {prospect_id: prospect_id}, 'check_prospect','#result-ajax', true, function (output) {
                    if(output){
                        if(output.p_id.length > 0){
                            resultProspect = [output];
                            messageElement.html('<p style="font-size: 12px; color: green"><i class="fas fa-check-circle" style="margin-right: 5px;"></i>Prospect valide</p>');

                            if($('#type').val() != 0){
                                updateChampCommand();
                            }
                        }
                        else{
                            messageElement.html('<p style="font-size: 12px; color: red"><i class="fas fa-times-circle" style="margin-right: 5px;"></i>Prospect introuvable</p>');
                        }
                    }
                    else{
                        messageElement.html('<p style="font-size: 12px; color: red"><i class="fas fa-times-circle" style="margin-right: 5px;"></i>Prospect introuvable</p>');
                    }
                });
            }
            else{
                messageElement.html('<p style="font-size: 12px; color: red"><i class="fas fa-times-circle" style="margin-right: 5px;"></i>Le code prospect doit commencer par la lettre P</p>');
            }
        }
        else{
            messageElement.html('<p style="font-size: 12px; color: red"><i class="fas fa-times-circle" style="margin-right: 5px;"></i>Le code prospect n\'est pas renseigné</p>');
        }
    }
})


$(document).on('input', '#lot_id', function () {
    let target = $(this).data('target');
    if($(this).hasClass('input-dependant')){
        let error = true;
        let lot_id = $(this).val();
        let messageElement = $('#result-search-lot');
        messageElement.html('');

        if(lot_id.length > 0){
            sendAJAX('script', {lot_id: lot_id}, 'check_lot','#result-ajax', true, function (output) {
                if(output){
                    if(output.lot_code.length > 0){
                        resultLot = [output];
                        messageElement.html('<p style="font-size: 12px; color: green"><i class="fas fa-check-circle" style="margin-right: 5px;"></i>Lot valide</p>');

                        if($('#type').val() != 0){
                            updateChampCommand();
                        }
                    }
                    else{
                        messageElement.html('<p style="font-size: 12px; color: red"><i class="fas fa-times-circle" style="margin-right: 5px;"></i>Lot introuvable</p>');
                    }
                }
                else{
                    messageElement.html('<p style="font-size: 12px; color: red"><i class="fas fa-times-circle" style="margin-right: 5px;"></i>Lot introuvable</p>');
                }
            });
        }
        else{
            messageElement.html('<p style="font-size: 12px; color: red"><i class="fas fa-times-circle" style="margin-right: 5px;"></i>Le code lot n\'est pas renseigné</p>');
        }
    }
})

// Remplissage formulaire selon le type selectionné
$('#type').change(function () {
    // let confirmUpdateForm = confirm('Voulez vous pré-remplir le formulaire avec les informations par défaut de l\'offre ?')
    let confirmUpdateForm = true;
    let typeSelected = offreType.filter(typeObj => typeObj.typ_id == $(this).val())[0];

    if(confirmUpdateForm && typeSelected){
        $('#libelle').val(typeSelected.typ_lib)

        updateChampCommand();

        if(resultProspect){
            prospect = resultProspect[0];
        }
        if(resultLot){
            lot = resultLot.find(l => l.lot_code == $('#lot_id').val());
        }

        if(resultPartenaire){
            partenaire = resultLot.find(l => l.id == $('#partenaire_id').val());
        }

        if(typeSelected.typ_attend_valeur == 1){
            $('#wrapper-box-valeur').show();
            $('#wrapper-box-valeur').prop('required', true);
        }
        else{
            $('#wrapper-box-valeur').hide();
        }
    }

});

function updateChampCommand(){
    let typeSelected = offreType.filter(typeObj => typeObj.typ_id == $('#type').val())[0];

    if(typeSelected.typ_description){
        (async () =>{
            let descriptionStr = await renderWithCommand(typeSelected.typ_description);
            $('#description').val(descriptionStr)
        })()
    }
    if(typeSelected.typ_condition){
        (async () =>{
            let conditionStr = await renderWithCommand(typeSelected.typ_condition);
            $('#condition').val(conditionStr)
        })()
    }
}

async function renderWithCommand(text){
    const words = text.split(/(\s+)/);
    let regExp = /{{([^)]+)}}/

    const output = words.map((word) => {
        let wordReg = regExp.exec(word);
        if(wordReg){
            if(listeCommand.includes(wordReg[1])){
                let keyCommand = listeCommandBD[getKeyByValue(listeCommand, wordReg[1])]
                if(keyCommand){
                    if(keyCommand.table == 'prospect'){
                        if(resultProspect){
                            prospect = Array.isArray(resultProspect) ? resultProspect[0] : resultProspect;
                            return prospect[keyCommand.champs];
                        }
                    }
                    else if(keyCommand.table == 'lot'){
                        if(resultLot){
                            lot = resultLot.find(l => l.lot_code == $('#lot_id').val());
                            return lot[keyCommand.champs];
                        }
                    }
                }
            }
        }

        return word;
    })
    return output.join('');
}



function renderWithCommandBDValue(text){
    const words = text.split(/(\s+)/);
    let regExp = /{{([^)]+)}}/

    const output = words.map((word) => {
        let wordReg = regExp.exec(word);
        if(wordReg){
            if(listeCommand.includes(wordReg[1])){
                let keyCommand = listeCommandBD[getKeyByValue(listeCommand, wordReg[1])]
                if(keyCommand){
                    return offre[wordReg[1]]
                }
            }
        }

        return word;
    })
    return output.join('');
}




$('#btn-search-prospect').click(function () {
    $('#popup-search-prospect').show()
})

$('#btn-search-lot').click(function () {
    $('#popup-search-lot').show()
})

$('#btn-search-partenaire').click(function () {
    $('#popup-search-partenaire').show()
})




$('#btn-add-champ').click(function () {
    const tableChamps = $('#table-add-champs');
    const rowTr = document.createElement('tr');
    const td = document.createElement('tr');
    const cloneRow = $('#row-form-champ').clone();
    const rowLength = tableChamps.find('tr').length - 2;

    // cloneRow.find('#champs-is-visible').attr()

    $('#popup-ajout-champ').show();
});

/**
 * Permet de savoir si le formulaire à été modifier. Ca servira a confirmé ou non l'écrasement du formulaire modifié
 * @param modified
 */
function setModifiedForm(modified = true){
    hasModifiyForm = modified;
}



$(document).on('input', '#recherche_prospect', function () {
    $('.result-row-prospect').remove();
    sendAJAX('script', {text_field: $(this).val(), limit: 30}, 'recherche_prospect','#result-ajax', true, function (output) {

        $('.result-row-prospect').remove();
        if(output){
            if(output['total'] !== undefined){
                $('#commentaire-result-prospect').html('Total résultats : <b id="total-result-prospect">'+output['total']+'</b>');
            }
            if(output['result'] !== undefined){
                resultProspect = output['result'];
                for(let i =0 ; i < output['result'].length; i++){
                    const trElement = document.createElement('tr');
                    trElement.classList.add('result-row-prospect');

                    // ajout du code prospect dans la liste de resultat de la recherche
                    resultProspect[i].code_prospect = 'P' + output['result'][i]['p_id'].padStart(6, '0');

                    trElement.append(contructTDElement('<input type="radio" class="checkbox-row" name="prospect_search_id" value="'+'P' + output['result'][i]['p_id'].padStart(6, '0')+'">'))
                    trElement.append(contructTDElement('P' + output['result'][i]['p_id'].padStart(6, '0')))
                    trElement.append(contructTDElement(output['result'][i]['p_nom'] + ', ' + output['result'][i]['p_prenom']))
                    trElement.append(contructTDElement(output['result'][i]['p_email']))
                    trElement.append(contructTDElement(output['result'][i]['p_telephone']))
                    trElement.append(contructTDElement(output['result'][i]['p_creationdate']))

                    $('#table-prospect').append(trElement);
                }
            }
        }
    })
});

$(document).on('input', '#recherche_lot', function () {
    $('.result-row-lot').remove();
    sendAJAX('script', {text_field: $(this).val(), limit: 30}, 'recherche_lot','#result-ajax', true, function (output) {
        $('.result-row-lot').remove();
        if(output){
            if(output['total'] !== undefined){
                $('#commentaire-result-lot').html('Total résultats : <b id="total-result-lot">'+output['total']+'</b>');
            }
            if(output['result'] !== undefined){
                resultLot = output['result'];
                for(let i =0 ; i < output['result'].length; i++){
                    const trElement = document.createElement('tr');
                    trElement.classList.add('result-row-lot');

                    trElement.append(contructTDElement('<input type="radio" class="checkbox-row" name="lot_search_id" value="'+output['result'][i]['lot_code']+'">'))
                    trElement.append(contructTDElement(output['result'][i]['prog_code']))
                    trElement.append(contructTDElement(output['result'][i]['prog_lib']))
                    trElement.append(contructTDElement(output['result'][i]['prog_liblong']))
                    trElement.append(contructTDElement(output['result'][i]['lot_code']))
                    trElement.append(contructTDElement(output['result'][i]['lot_numero_usuel']))
                    trElement.append(contructTDElement(output['result'][i]['lot_batiment']))
                    trElement.append(contructTDElement(output['result'][i]['adr_adresse']))
                    trElement.append(contructTDElement(output['result'][i]['adr_codepostal']))
                    trElement.append(contructTDElement(output['result'][i]['adr_localite']))

                    $('#table-lot').append(trElement);
                }
            }
        }
    })
});


$(document).on('input', '#recherche_partenaire', function () {
    $('.result-row-partenaire').remove();
    sendAJAX('script', {text_field: $(this).val(), limit: 30}, 'recherche_partenaire','#result-ajax', true, function (output) {
        $('.result-row-partenaire').remove();
        if(output){
            if(output['total'] !== undefined){
                $('#commentaire-result-partenaire').html('Total résultats : <b id="total-result-partenaire">'+output['total']+'</b>');
            }
            if(output['result'] !== undefined){
                resultPartenaire = output['result'];

                for(let i =0 ; i < output['result'].length; i++){
                    const trElement = document.createElement('tr');
                    trElement.classList.add('result-row-partenaire');

                    trElement.append(contructTDElement('<input type="radio" class="checkbox-row" name="partenaire_search_id" value="'+output['result'][i]['id']+'">'))
                    trElement.append(contructTDElement(output['result'][i]['Partenaire']))
                    trElement.append(contructTDElement(output['result'][i]['Reseau']))
                    trElement.append(contructTDElement(output['result'][i]['Titre']))
                    trElement.append(contructTDElement(output['result'][i]['Email']))
                    trElement.append(contructTDElement(output['result'][i]['Tels']))
                    trElement.append(contructTDElement(output['result'][i]['Ville']))

                    $('#table-partenaire').append(trElement);
                }
            }
        }
    })
});

$(document).on('click', '.result-row-prospect', function (e) {
    if(!e.target.hasAttribute('type')){
        const checkbox = $(this).find('.checkbox-row');
        checkbox.prop('checked', !checkbox.is(':checked'))
    }
})

$(document).on('click', '.result-row-partenaire', function (e) {
    if(!e.target.hasAttribute('type')){
        const checkbox = $(this).find('.checkbox-row');
        checkbox.prop('checked', !checkbox.is(':checked'))
    }
})

$(document).on('click', '.result-row-lot', function (e) {
    if(!e.target.hasAttribute('type')){
        const checkbox = $(this).find('.checkbox-row');
        checkbox.prop('checked', !checkbox.is(':checked'))
    }
})

$(document).on('click', '#btn-select-prospect', function () {
    const checkbox = $(this).find('.checkbox-row');
    const inputSelected = $('.result-row-prospect td input[name="prospect_search_id"]:checked')
    resultProspect = resultProspect.find(p => p.code_prospect == inputSelected.val());
    $('#prospect_id').val(inputSelected.val())
    $('#popup-search-prospect').hide();
    $('#prospect_id').trigger('input');
    $('#prospect_id').trigger('change');

    if($('#type').val() != 0){
        updateChampCommand()
    }
})



$(document).on('click', '#btn-select-lot', function () {
    const checkbox = $(this).find('.checkbox-row');
    const inputSelected = $('.result-row-lot td input[name="lot_search_id"]:checked')
    resultLot = resultLot.find(l => l.lot_code == inputSelected.val());
    $('#lot_id').val(inputSelected.val())
    $('#popup-search-lot').hide()
    $('#lot_id').trigger('input');
    $('#lot_id').trigger('change');
    if($('#type').val() != 0){
        updateChampCommand()
    }
})


$(document).on('click', '#btn-select-partenaire', function () {
    const checkbox = $(this).find('.checkbox-row');
    const inputSelected = $('.result-row-partenaire td input[name="partenaire_search_id"]:checked')
    $('#partenaire_id').val(inputSelected.val())
    $('#popup-search-partenaire').hide()
})



function contructTDElement(value){
    const tdElement = document.createElement('td');
    tdElement.innerHTML = value;

    return tdElement;
}

/* ------------------------------------------------ PARTIE PDF ----------------------------------------- */
// On génére le document si n'est pas déjà généré
if(page === 'show_offre'){
    if(hasToGenerate){
        $(document).on('click', '#btn-generate', function () {
            execScriptGenerate(templateURL);
        })
        // affichageDocumentOffre(templateURL, champs);
    }
    else{
        affichageDocumentOffre(offreURL);
    }
}

function execScriptGenerate(fileURL){
    const file_data = fileURL;
    const request = new XMLHttpRequest();


    request.open('GET', fileURL, true);
    request.responseType = 'blob';
    request.onload = function () {
        const reader = new FileReader();

        reader.onload = function(e) {
            let data = e.target.result;
            let h_sha256 = CryptoJS.SHA256(CryptoJS.enc.Latin1.parse(data)).toString();


            sendAJAX('script', {offre_id: offre.ofr_id, libelle: offre.ofr_lib, size: file_data.size, description: '', code: h_sha256}, 'create_document','#result-ajax', true, function (output) {

                if(output){
                    const fileNamePredefini = output.filename;


                    // let tempValues = {code1_before: h_sha256Avant, code2_before: h_sha3_1Avant, user_id: user.id , filename: fileNamePredefini, filesize: 1};

                    modifyPdfGenerate(data, fileNamePredefini + '.pdf', user, output)
                }
            })
        };
        reader.readAsDataURL(request.response); // convert to base64 string
    };
    request.send();
}

async function modifyPdfGenerate(documentUrl, fileNameOrigin, user, resutl = []){
    const { degrees, PDFDocument, rgb, StandardFonts } = PDFLib;
    const url = documentUrl;
    const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer());
    const pdfDoc = await PDFDocument.load(existingPdfBytes, {ignoreEncryption: true});
    const var_pages = pdfDoc.getPages();
    const page = var_pages[0];
    const { width, height } = page.getSize();
    const helveticaFont = await pdfDoc.embedFont(StandardFonts.Helvetica);

    const form = pdfDoc.getForm();
    const fields = form.getFields()

    const existingField = [];
    fields.forEach(field => {
        existingField.push(field.getName())
    })


    for(let i = 0; i < champsOffre.length; i++){
        if(champsOffre[i]['cha_input_name']){
            if(champsOffre[i]['cha_input_name'].length > 0 && existingField.includes(champsOffre[i]['cha_input_name'])){
                let inputDocuOffre = form.getTextField(champsOffre[i]['cha_input_name']);
                let nomChamp = champsOffre[i]['cha_bdd'].split('.');
                    // renderWithCommand


                if(offre[nomChamp[1]]){
                    let tempValue = renderWithCommandBDValue(offre[nomChamp[1]]);
                    inputDocuOffre.setText(tempValue);
                }
            }
        }
        else if(champsOffre[i]['cha_posx']){
            if(champsOffre[i]['cha_posx'].length > 0){
                const PosY = parseInt(champsOffre[i]['cha_posy']);
                const PosX = parseInt(champsOffre[i]['cha_posx']);

                let positionX = ((PosX / gridDocumentOffre.x) * width) - 11;
                if(PosX > 0){
                    // positionX -= 4;
                }

                let positionY = ((PosY / gridDocumentOffre.y) * height);
                if(PosY > 0){
                    positionY -= 5;
                }

                let nomChamp = champsOffre[i]['cha_bdd'].split('.');

                if(offre[nomChamp[1]]){
                    page.drawText(offre[nomChamp[1]], {
                        x: positionX,
                        y: positionY,
                        size: 12,
                        font: helveticaFont
                    });
                }
            }
        }
    }



    let checkValueFormulaire = champsOffre.find((o, i) =>{
        return o.champ_id === 8;
    })

    form.flatten();

    let svgNode  = new BARCode({
        msg :  resutl.code
        ,dim : [ 220, 50]
        ,pal : ["#000000"]
        ,pad : [ 0, 0]
    });

    // On ajoute les elements placer directement dans le formulaire du PDF


    // On ajoute les elements placer dans la grille
    const tamponLargeur = 1; // le tampon
    const tamponHauteur = 1; // le tampon


    // CODE BARRE
    let svgText =svgNode.children[0].getAttribute('d');
    page.moveTo(225, 40);
    svgText = resutl.svgbarcode
    page.drawSvgPath(svgText, { fillOpacity: 1, borderColor:  rgb(0, 0, 0), borderWidth: 0, scale: 0.60});

    // CODE CODE BARRE





    const pdfBytes = await pdfDoc.save();
    const pdfBlob = new Blob([pdfBytes], {type: 'application/pdf'});


    const newReader = new FileReader();


    await createFormData(pdfBlob, fileNameOrigin, resutl.filepath)


    newReader.onload = async function (e) {
        const newData = e.target.result
        download(pdfBytes, fileNameOrigin, "application/pdf");
        // $('#container-popup-form-generate').
    }
    newReader.readAsDataURL(pdfBlob);
}

async function createFormData(blobData, fileName, filepath){
    const objectUrl = URL.createObjectURL(blobData);
    const dataForm = new FormData();
    dataForm.append('name', fileName);
    dataForm.append('tmp_name', objectUrl);
    dataForm.append('path', filepath);
    dataForm.append('data', blobData, fileName);
    dataForm.append('action', 'generate_offre');

    let response = await fetch('script-ajax.php', {
        method: 'POST',
        body: dataForm
    });

    let result = await response.json();

    window.location.href = 'index.php?p=offre&id='+offre.ofr_id;
}

$('#container-popup-form-generate').submit(function () {

    return false;
})

$('.box-img-offre-grid').each(function () {
    affichageDocumentOffreListe($(this).attr("data-path"), $(this)).then(r => {

        $('#text-condition-offre').html()
    })
})

async function affichageDocumentOffreListe(pdfURL, container){
    let pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'library/pdfjs-2.4.456-dist/build/pdf.worker.js';
    let loadingTask;
    loadingTask = pdfjsLib.getDocument(pdfURL);
    loadingTask.promise.then(function(pdf) {
        let nbPageToShow = pdf.numPages;
        container.empty();

        for(let pageNumber = 1; pageNumber < 2; pageNumber++){
            pdf.getPage(pageNumber).then(function(page) {
                renderOffrePdfListe(pdfURL, page, container, pageNumber);
            });
        }

        // Ajouter la partie de gauche d'un document (le bouton dignature et infos etc)


    }, function (reason) {
        // PDF loading error
        // console.error(reason);
    });
}


async function affichageDocumentOffre(pdfURL){
    let pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'library/pdfjs-2.4.456-dist/build/pdf.worker.js';
    let loadingTask;

    const container = $('#pdfViewer');

    loadingTask = pdfjsLib.getDocument(pdfURL);

    loadingTask.promise.then(function(pdf) {
        let nbPageToShow = pdf.numPages;
        container.empty();

        for(let pageNumber = 1; pageNumber < 2; pageNumber++){
            pdf.getPage(pageNumber).then(function(page) {
                renderOffrePdf(pdfURL, page, container, pageNumber);
            });
        }

        // Ajouter la partie de gauche d'un document (le bouton dignature et infos etc)


    }, function (reason) {
        // PDF loading error
        // console.error(reason);
    });
}


async  function renderOffrePdf(pdfURL, page, container, pageNum, champs = []){
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

    const existingPdfBytes = await fetch(pdfURL).then(res => res.arrayBuffer());
    const pdfDoc = await PDFDocument.load(existingPdfBytes, {ignoreEncryption: true});
  }


async  function renderOffrePdfListe(pdfURL, page, container, pageNum){
    const { degrees, PDFDocument, rgb, StandardFonts } = PDFLib;
    let viewport = page.getViewport({scale: container.width() / page.getViewport({scale: 1}).width});
    // Prepare canvas using PDF page dimensions
    const canvasWrap = container;
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    canvas.height = viewport.height;
    canvas.width = viewport.width;


    page.getAnnotations().then(function(items) {
        documentFieldsForm = items;
    });



    canvasWrap.append(canvas);

    // Render PDF page into canvas context
    const renderContext = {
        canvasContext: context,
        viewport: viewport
    };
    const renderTask = page.render(renderContext);

    const existingPdfBytes = await fetch(pdfURL).then(res => res.arrayBuffer());
    const pdfDoc = await PDFDocument.load(existingPdfBytes, {ignoreEncryption: true});
  }