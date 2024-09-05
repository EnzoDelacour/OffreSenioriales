$(function() {
    $('.datepicker').datepicker({
        firstDay: 1,
        altField: "#datepicker",
        closeText: 'Fermer',
        prevText: 'Précédent',
        nextText: 'Suivant',
        currentText: 'Aujourd\'hui',
        monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
        dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
        weekHeader: 'Sem.',
        dateFormat: 'dd/mm/yy'
    })
});



$('.select-choosen').chosen();


// Affichage tooltips
$('.pattern-tooltip-element').hover(
    function () {
        $(this).parent().find('.pattern-tooltip-message').toggleClass('pattern-tooltip-show');
    }
);


// Close popup
$(document).on('click', '.btn-cancel-popup', function () {
    $(this).closest('.popup-container-resultat').hide();
    $('body').css({"position": "relative"});
});

$(document).on('click', '.popup-container-resultat', function (e) {
    if($(e.target).hasClass('popup-content-resultat')){
        $(this).hide();
        $('body').css({"position": "relative"});
    }
});

$(document).on('click', '.box-exit-notification', function (e) {
    $(this).closest('.container-notification').remove();
});

/**
 * Ouverture du menu
 */
$('#box-profil').click(function () {
    if($(this).hasClass('box-profil-selected')){
        $(this).removeClass('box-profil-selected');
        $('#wrapper-box-menu-sup').hide();
    }
    else{
        $(this).addClass('box-profil-selected');
        $('#wrapper-box-menu-sup').show();
    }
})

function getKeyByValue(object, value) {
    return Object.keys(object).find(key => object[key] === value);
}

function sendAJAX(onglet, values, action, container, returnJSON = false, handleCallBack = null){
    var jsonValues = JSON.stringify(values);

    $.ajax({
        type: "POST",
        url: "script-ajax.php",
        timeout: 3000,
        data: {
            action: action,
            values: jsonValues
        },
        success: function (result){
            if(returnJSON){
                try{
                    result = JSON.parse(result);
                }
                catch (e) {
                    console.log("L'action " + action + " ne retourne pas de JSON :");
                    console.log(result);

                }
                handleCallBack(result);
            }
            else {
                $(container).html(result);
            }
        }
    });
}

function decodeHTMLEntities(text) {
    const textArea = document.createElement('textarea');
    textArea.innerHTML = text;
    return textArea.value;
}

function FileConvertSize(aSize){
    aSize = Math.abs(parseInt(aSize, 10));
    var def = [[1, 'octets'], [1024, 'ko'], [1024*1024, 'Mo'], [1024*1024*1024, 'Go'], [1024*1024*1024*1024, 'To']];
    for(var i=0; i<def.length; i++){
        if(aSize<def[i][0]) return (aSize/def[i-1][0]).toFixed(2)+' '+def[i-1][1];
    }
}



$(document).ready(function() {
    if (typeof arrXAxis !== 'undefined') {
        const chart_type = $('#graphique_stats').val();

        $('.select-stats').change(function () {
            const periode = $('#periode').val()
            const temps = $('#temps').val()
            const indicateur = $('#indicateur').val()
            const graphique = $('#graphique').val()
            const destination = $('#destination').val()
            const residence = $('#résidence').val()
            const type = $('#type').val()
            const statut = $('#statut').val()

            $('#link-stats').attr('href', `index.php?p=stats&per=${periode}&tem=${temps}`)
        })


        Highcharts.setOptions({
            lang: {
                downloadPNG: 'Télécharger image PNG',
                downloadJPEG: 'Télécharger image JPEG',
                downloadPDF: 'Télécharger document PDF',
                downloadSVG: 'Télécharger image SVG',
                downloadCSV: 'Télécharger CSV',
                downloadXLS: 'Télécharger XLS',
                hideData: 'Masquer le tableau',
                printChart: 'Imprimer graphique',
                viewData: 'Afficher le tableau',
                viewFullscreen: 'Plein écran',
            }
        });

        if(customColor.length > 0){
            Highcharts.setOptions({
                colors: customColor
            });
        }

        Highcharts.chart('container-graphique', {
            exporting: {
                showTable: true,
                tableCaption: 'Graphique',
                csv:{
                    columnHeaderFormatter: function (item, key) {
                        if (!item || item instanceof Highcharts.Axis) {
                            return 'Date' ;
                        }
                        // Item is not axis, now we are working with series.
                        // Key is the property on the series we show in this column.
                        // return '<div class="ds-header-table"><div class="ds-header-table-dot" style="background: '+item.color+'"></div><span>'+item.name+'</span></div>';
                        return item.name;
                    }
                },
            },
            chart: {
                height: 600,
                type: chart_type,
            },
            title: {
                text: 'Graphique statistique'
            },

            subtitle: {
                text: ''
            },

            yAxis: {
                title: {
                    text: "Nombre d'offre"
                },
                labels: {
                    format: '{value}'
                }
            },

            tooltip: {
                valueSuffix: '',

                useHTML: true,
                headerFormat: '<small>{point.key}</small><table>',
                pointFormat: '<tr><td style="color: {series.color}">{series.name}: </td>' +
                    '<td style="text-align: right"><b>{point.y}</b></td></tr>',
                footerFormat: '</table>',
            },

            xAxis: {
                categories: arrXAxis
            },

            legend: {
                layout: chart_type === 'column' ? 'horizontal' : 'vertical',
                align: chart_type === 'column' ? 'center' : 'right',
                verticalAlign: chart_type === 'column' ? 'bottom' : 'middle'
            },

            series: series,

            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function (){
                                let link = rootDir + 'index.php?iframe=liste_offre'
                                link += '&xAxis=' + this.category
                                link += '&yAxis=' + this.series.name
                                link += '&temps=' + tempsStat
                                link += '&indicateur=' + indicateurStat
                                link += '&destination=' + destinationStat
                                link += '&type_offre=' + $typeOffreStat
                                link += '&residence=' + residenceStat
                                link += '&statut=' + statut
                                $('#iframe-detail-stat').attr('src', link)
                                $('#popup-detail-stats').show()
                                console.log([this.series.name, this.category, indicateurStat, tempsStat, destinationStat, $typeOffreStat, residenceStat, statut]);
                            }
                        }
                    }
                }
            },

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            },
            credits: {
                enabled: false
            },

        });
    }
});















