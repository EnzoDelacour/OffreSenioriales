$(document).on('input', '#recherche_prospect', function () {
    $('.resurlt-row-prospect').remove();
    sendAJAX('script', {text_field: $(this).val(), limit: 30}, 'recherche_prospect','#result-ajax', true, function (output) {

        $('.resurlt-row-prospect').remove();
        if(output){
            if(output['total'] !== undefined){
                $('#commentaire-result-prospect').html('Total résultats : <b id="total-result-prospect">'+output['total']+'</b>');
            }
            if(output['result'] !== undefined){
                for(let i =0 ; i < output['result'].length; i++){
                    const trElement = document.createElement('tr');
                    trElement.classList.add('result-row-prospect');

                    trElement.append(contructTDElement('<input type="radio" class="checkbox-row" name="prospect_search_id" value="'+output['result'][i]['p_id']+'">'))
                    trElement.append(contructTDElement(output['result'][i]['p_id']))
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

$(document).on('click', '.result-row-prospect', function () {
    const checkbox = $(this).find('.checkbox-row');

    checkbox.prop('checked', !checkbox.is(':checked'))
})



// const editorDescription = document.querySelector('#description-editor');
// // updateEditor(editorDescription);
// editorDescription.addEventListener('input', function () {
//     // updateEditor(editorDescription);
// });
//
// const editorcCondition = document.querySelector('#condition-editor');
// // updateEditor(editorcCondition);
// editorcCondition.addEventListener('input', function () {
//     // updateEditor(editorcCondition);
// });

$('#btn-create-type').click(function () {
    // $('#description').val(editorDescription.innerText)
    // $('#condition').val(editorcCondition.innerText)
    $('#form-type').submit()
})

//
// function getTextSegments(element) {
//     const textSegments = [];
//     Array.from(element.childNodes).forEach((node) => {
//         switch(node.nodeType) {
//             case Node.TEXT_NODE:
//                 textSegments.push({text: node.nodeValue, node});
//                 break;
//
//             case Node.ELEMENT_NODE:
//                 textSegments.splice(textSegments.length, 0, ...(getTextSegments(node)));
//                 break;
//
//             default:
//                 throw new Error(`Unexpected node type: ${node.nodeType}`);
//         }
//     });
//     return textSegments;
// }
//
//
// function updateEditor(editor) {
//     const sel = window.getSelection();
//     const textSegments = getTextSegments(editor);
//     const textContent = textSegments.map(({text}) => text).join('');
//     let anchorIndex = null;
//     let focusIndex = null;
//     let currentIndex = 0;
//     textSegments.forEach(({text, node}) => {
//         if (node === sel.anchorNode) {
//             anchorIndex = currentIndex + sel.anchorOffset;
//         }
//         if (node === sel.focusNode) {
//             focusIndex = currentIndex + sel.focusOffset;
//         }
//         currentIndex += text.length;
//     });
//
//     editor.innerHTML = renderText(editor, textContent);
//
//     restoreSelection(editor, anchorIndex, focusIndex);
// }
//
// function restoreSelection(editor, absoluteAnchorIndex, absoluteFocusIndex) {
//     const sel = window.getSelection();
//     const textSegments = getTextSegments(editor);
//     let anchorNode = editor;
//     let anchorIndex = 0;
//     let focusNode = editor;
//     let focusIndex = 0;
//     let currentIndex = 0;
//     textSegments.forEach(({text, node}) => {
//         const startIndexOfNode = currentIndex;
//         const endIndexOfNode = startIndexOfNode + text.length;
//         if (startIndexOfNode <= absoluteAnchorIndex && absoluteAnchorIndex <= endIndexOfNode) {
//             anchorNode = node;
//             anchorIndex = absoluteAnchorIndex - startIndexOfNode;
//         }
//         if (startIndexOfNode <= absoluteFocusIndex && absoluteFocusIndex <= endIndexOfNode) {
//             focusNode = node;
//             focusIndex = absoluteFocusIndex - startIndexOfNode;
//         }
//         currentIndex += text.length;
//     });
//
//     sel.setBaseAndExtent(anchorNode,anchorIndex,focusNode,focusIndex);
// }
//
//
// function renderText(editor, text) {
//     const words = text.split(/(\s+)/);
//     let regExp = /{{([^)]+)}}/
//     const output = words.map((word) => {
//         let wordReg = regExp.exec(word);
//         if(wordReg){
//             if(listeCommand.includes(wordReg[1])){
//                 return `<span><b style="color: #3498db">{{</b><strong>${wordReg[1]}</strong><b style="color: #3498db">}}</b></span>`;
//             }
//             else{
//                 return `<span><b style="color: #3498db">{{</b><strong style="color: red" title="Cette commande n'existe pas !">${wordReg[1]}</strong><b style="color: #3498db">}}</b></span>`;
//             }
//         }
//         if (word === 'bold') {
//             return `<strong>${word}</strong>`;
//         }
//         else if (word === 'red') {
//             return `<span style='color:red'>${word}</span>`;
//         }
//         else {
//             return word;
//         }
//     })
//     return output.join('');
// }

// Affichage template dans la liste
$(document).ready(function () {
    $('.box-image-template-liste').each(function () {
        affichageDocumentOffreListe($(this).attr("data-path"), $(this)).then(r => {

        })
    })


    $(document).on('click', '.wrapper-box-image-template-liste', function () {
        console.log('test');
        $('#popup-show-type-offre').show();
        affichageDocumentOffreListe($(this).attr("data-path"), $('#appercu-type-offre')).then(r => {

        })
    })



})


// Masque les text long du tableau
if (typeof listeType   === 'undefined') {
    listeType   = undefined;
}
$('.has-long-text-hidden').click(function () {
    if(listeType !== 'undefined'){
        const typeId = $(this).closest('.row-typeoffre-liste').attr('data-type-id');
        const columnName = $(this).attr('data-column');
        const getSelectedType = listeType.find((element) => element.typ_id == typeId)

        if(getSelectedType[columnName]){
            if($(this).attr('data-show') === '0'){
                $(this).text(getSelectedType[columnName]);
                $(this).attr('data-show', 1);
            }
            else{
                $(this).text(getSelectedType[columnName].substring(0, 100) + '...')
                $(this).attr('data-show', 0);
            }
        }
    }
})


// Set actif type d'offre
$('.input-set-actif').change(function () {
    const visibility = $(this).is(':checked') ? 'visible' : 'invisible';

    if(confirm(`Rendre ce type d'offre ${visibility} pour les commerciaux ?`)){
        $(this).closest('form').submit()
    }
})