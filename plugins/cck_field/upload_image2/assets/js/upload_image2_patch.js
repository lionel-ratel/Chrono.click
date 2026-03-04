/**
 * SEBLOD Upload Image 2 - Patch pour corrections de calculs
 * 
 * Ce fichier doit être chargé APRÈS upload_image2.js
 * Il surcharge les fonctions problématiques avec des versions corrigées.
 * 
 * @version     1.1.0
 * @package     SEBLOD
 * 
 * CORRECTIONS:
 * 1. Arrondis cohérents (Math.round partout)
 * 2. Calcul du scale sans bordures CSS
 * 3. Gestion du cas PL=2 (dimensions exactes)
 * 4. Validation des coordonnées avant envoi
 */

(function($) {
    'use strict';

    // Vérifier que JCck.More.CropX existe
    if (typeof JCck === 'undefined' || typeof JCck.More === 'undefined' || typeof JCck.More.CropX === 'undefined') {
        console.error('CropX Patch: JCck.More.CropX not found. Load this after upload_image2.js');
        return;
    }

    var CropX = JCck.More.CropX;

    // =========================================================================
    // UTILITAIRES DE CALCUL CORRIGÉS
    // =========================================================================

    /**
     * Arrondir de manière cohérente (même comportement que PHP round())
     */
    function roundCoord(value) {
        return Math.round(value);
    }

    /**
     * Garantir qu'une valeur est dans les limites
     */
    function clamp(value, min, max) {
        return Math.max(min, Math.min(max, value));
    }

    /**
     * Valider et corriger les coordonnées de sélection
     */
    function validateSelection(selection, maxW, maxH) {
        var sel = {
            x1: roundCoord(selection.x1),
            y1: roundCoord(selection.y1),
            x2: roundCoord(selection.x2),
            y2: roundCoord(selection.y2),
            width: roundCoord(selection.width),
            height: roundCoord(selection.height)
        };

        // S'assurer que x1 < x2 et y1 < y2
        if (sel.x1 > sel.x2) {
            var tmp = sel.x1;
            sel.x1 = sel.x2;
            sel.x2 = tmp;
        }
        if (sel.y1 > sel.y2) {
            var tmp = sel.y1;
            sel.y1 = sel.y2;
            sel.y2 = tmp;
        }

        // Recalculer width/height
        sel.width = sel.x2 - sel.x1;
        sel.height = sel.y2 - sel.y1;

        // Valider les limites (CORRECTION: pas de -1 ou -2 magiques)
        sel.x1 = clamp(sel.x1, 0, maxW - sel.width);
        sel.y1 = clamp(sel.y1, 0, maxH - sel.height);
        sel.x2 = sel.x1 + sel.width;
        sel.y2 = sel.y1 + sel.height;

        return sel;
    }

    /**
     * Calculer le scale sans inclure les bordures CSS
     * CORRECTION du bug: getBoundingClientRect() inclut les bordures
     */
    function getAccurateScale(element, referenceWidth) {
        if (!element) return 1;
        
        // Utiliser offsetWidth au lieu de getBoundingClientRect().width
        // car offsetWidth n'inclut pas les transformations CSS
        var computedStyle = window.getComputedStyle(element);
        var borderLeft = parseFloat(computedStyle.borderLeftWidth) || 0;
        var borderRight = parseFloat(computedStyle.borderRightWidth) || 0;
        var actualWidth = element.offsetWidth - borderLeft - borderRight;
        
        return actualWidth / referenceWidth;
    }

    // =========================================================================
    // SURCHARGE DE getThumb - CORRECTION PRINCIPALE
    // =========================================================================

    var originalGetThumb = CropX.getThumb;

    CropX.getThumb = function(that) {
        var data = CropX.getData($('#toolbar-crop'));
        data['thumb'] = $('#toolbar-crop .dropdown-toggle').attr('data-value');
        $(that).parent().attr('data-thumb', data['thumb']);
        data = CropX.getSize(data);

        $.ajax({
            cache: false,
            url: CropX.link + '&t=getThumb',
            data: { data: data },
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                CropX.addProgress();
            },
            success: function(response) {
                $('#toolbar-crop').attr({
                    'data-thumb': response.thumb,
                    'data-pl': response.pl,
                    'data-wpl': response.wpl,
                    'data-hpl': response.hpl,
                    'data-cropped': response.cropped
                });

                // Add Placeholder
                $('div#resize-parent').html(response.placeholder);

                // Color Picker
                CropX.displayColor(
                    response.ext,
                    response.color,
                    response.picker,
                    response.palette
                );

                // PanZoom
                var btnzoom = $('.zoom-buttons').closest('.toolbar-btn');

                if (response.zoom) {
                    $('.set-expand,.set-contract').show();
                    btnzoom.show();

                    var element = document.getElementById('panzoom');

                    CropX.btnZoomOut = document.getElementById('zoom-out');
                    CropX.btnZoomIn = document.getElementById('zoom-in');
                    CropX.btnZoomRange = document.getElementById('zoom-range') || { value: 1 };
                    
                    CropX.panzoom = Panzoom(element, {
                        maxScale: 1,
                        minScale: 0.2,
                        step: 0.005,
                        contain: 'inside',
                        disablePan: true,
                        zoomWithWheel: false
                    });

                    CropX.panzoom.zoom(response.zoom);
                    CropX.btnZoomRange.value = response.zoom;

                    CropX.btnZoomRange.addEventListener('input', function(event) {
                        CropX.panzoom.zoom(event.target.valueAsNumber);
                    });

                    CropX.btnZoomIn.addEventListener('click', function() {
                        CropX.panzoom.zoomIn();
                        CropX.btnZoomRange.value = CropX.panzoom.getScale();
                    });

                    CropX.btnZoomOut.addEventListener('click', function() {
                        CropX.panzoom.zoomOut();
                        CropX.btnZoomRange.value = CropX.panzoom.getScale();
                    });
                } else {
                    if (response.pl == 2) {
                        $('.set-expand,.set-contract').hide();
                    }
                    btnzoom.hide();
                }

                // ImageAreaSelect - VERSION CORRIGÉE
                CropX.ias = $('#target').imgAreaSelect({
                    instance: true,
                    parent: $('div#resize-parent'),
                    handles: (response.pl == 2) ? false : true,
                    persistent: (response.pl == 2) ? true : false,
                    resizable: (response.pl == 2) ? false : true,
                    hide: true,
                    onInit: function(img, selection) {
                        var maxW = parseInt(response.wtrue, 10);
                        var maxH = parseInt(response.htrue, 10);

                        CropX.ias.setOptions({
                            imageWidth: maxW,
                            imageHeight: maxH,
                            minWidth: parseInt(response.wmin, 10),
                            minHeight: parseInt(response.hmin, 10),
                            aspectRatio: response.aspectRatio
                        });

                        // CORRECTION: Arrondis cohérents
                        var x = roundCoord(parseFloat(response.x) || 0);
                        var y = roundCoord(parseFloat(response.y) || 0);
                        var w = roundCoord(parseFloat(response.w) || response.wmin);
                        var h = roundCoord(parseFloat(response.h) || response.hmin);

                        // CORRECTION: Validation des limites sans valeurs magiques
                        x = clamp(x, 0, maxW - w);
                        y = clamp(y, 0, maxH - h);

                        // CORRECTION PL=2: Pour le cas des dimensions exactes,
                        // on centre l'image dans le thumb si pas déjà croppé
                        if (response.pl == 2 && !response.cropped) {
                            // L'image est positionnée dans le thumb, pas l'inverse
                            x = roundCoord((maxW - w) / 2);
                            y = roundCoord((maxH - h) / 2);
                        }

                        $('#resize-parent').css({
                            width: response.wpl,
                            height: response.hpl
                        }).children('div').css('position', 'absolute');

                        $('#target img#panzoom').css({
                            'top': '0',
                            'left': '0',
                            'position': 'absolute',
                            'width': response.wpl + 'px',
                            'height': response.hpl + 'px',
                            'display': 'block',
                            'max-width': 'none'
                        });

                        $('#target').css({
                            'width': response.wpl + 'px',
                            'height': response.hpl + 'px',
                            'overflow': 'hidden',
                            'display': 'block',
                            'position': 'relative'
                        });

                        CropX.ias.setSelection(x, y, x + w, y + h);
                        CropX.ias.setOptions({ fadeSpeed: 800, show: true });
                        CropX.ias.update();

                        CropX.updateColor(response.color);
                        CropX.removeProgress();
                        CropX.notify(CropX.i8n.loaded, 'success');

                        if (!response.cropped) {
                            if (response.pl == 2) {
                                $('.set-center').trigger('click');
                            } else {
                                var sl = (response.method) ? '.set-contract' : '.set-expand';
                                $(sl).trigger('click');
                            }
                            $('.set-crop').html(CropX.i8n.crop);
                        } else {
                            $('.set-crop').html(CropX.i8n.again);
                        }
                    }
                });
            },
            error: function() {
                CropX.removeProgress();
                CropX.notify('Error loading thumb', 'danger');
            }
        });
    };

    // =========================================================================
    // SURCHARGE DE crop - VALIDATION DES COORDONNÉES
    // =========================================================================

    CropX.crop = function(that) {
        var $toolbar = $(that).closest('#toolbar-crop');
        var data = CropX.getData($toolbar);
        var rawSelection = CropX.ias.getSelection();
        
        // CORRECTION: Valider les coordonnées
        var maxW = parseInt($toolbar.attr('data-wpl'), 10) || 1000;
        var maxH = parseInt($toolbar.attr('data-hpl'), 10) || 1000;
        
        // Récupérer les vraies dimensions si disponibles
        var options = CropX.ias.getOptions();
        if (options && options.imageWidth) {
            maxW = options.imageWidth;
            maxH = options.imageHeight;
        }
        
        data['selection'] = validateSelection(rawSelection, maxW, maxH);
        
        $('#crop-color').spectrum('hide');

        var color = $('#crop-color').spectrum('get');

        if (color && color.getAlpha() < 1) {
            data['color'] = '';
        } else if (color) {
            data['color'] = color.toHexString();
        } else {
            data['color'] = '';
        }

        data['matrix'] = 1;

        if ($('.zoom-range').is(':visible') && CropX.panzoom) {
            data['matrix'] = CropX.panzoom.getScale();
        }

        $.ajax({
            cache: false,
            url: CropX.link + '&t=cropThumbs',
            data: { data: data },
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                CropX.addProgress();
            },
            success: function(response) {
                $('input[name="' + response.name + '_version"]').val(response.version);
                $('.rotate[data-pk="' + response.pk + '"]').addClass('cropped');
                $('#toolbar-crop .dropdown-toggle span').removeClass('to-crop').addClass('cropped');
                var n = $('#toolbar-crop .dropdown-toggle').attr('data-value');
                $('#toolbar-crop .dropdown-menu').find('a[data-value="' + n + '"]').children('span').removeClass('to-crop').addClass('cropped');
                $('.set-crop').html(CropX.i8n.again);
                CropX.removeProgress();
                CropX.notify(CropX.i8n.cropped, 'success');
            },
            error: function() {
                CropX.removeProgress();
                CropX.notify(CropX.i8n.error, 'danger');
            }
        });
    };

    // =========================================================================
    // SURCHARGE DE expand - CALCUL DU SCALE CORRIGÉ
    // =========================================================================

    CropX.expand = function(that) {
        var data = CropX.getData($(that).closest('#toolbar-crop'));
        data['selection'] = CropX.ias.getSelection();

        $.ajax({
            cache: false,
            url: CropX.link + '&t=setExpand',  // Note: était setContract, bug?
            data: { data: data },
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                CropX.addProgress();
            },
            success: function(response) {
                CropX.ias.setSelection(
                    roundCoord(response.x),
                    roundCoord(response.y),
                    roundCoord(response.x + response.width),
                    roundCoord(response.y + response.height)
                );
                CropX.ias.setOptions({ fadeSpeed: 1000, show: true });
                CropX.ias.update();

                // CORRECTION: Calcul du scale sans bordures
                var target = document.getElementById('target');
                var wpl = parseInt(document.getElementById('toolbar-crop').getAttribute('data-wpl'), 10);
                
                // Utiliser la méthode corrigée
                var scale = getAccurateScale(target.nextElementSibling, wpl);

                if (CropX.btnZoomRange) {
                    CropX.btnZoomRange.value = scale;
                }
                if (CropX.panzoom) {
                    CropX.panzoom.zoom(scale);
                }

                CropX.center(that);
                CropX.removeProgress();
                CropX.notify(CropX.i8n.expanded, 'success');
            },
            error: function() {
                CropX.removeProgress();
            }
        });
    };

    // =========================================================================
    // SURCHARGE DE center - ARRONDIS COHÉRENTS
    // =========================================================================

    CropX.center = function(that) {
        var se = CropX.ias.getSelection();
        var op = CropX.ias.getOptions();
        
        // CORRECTION: Utiliser round() partout
        var x = roundCoord((op.imageWidth - se.width) / 2);
        var y = roundCoord((op.imageHeight - se.height) / 2);

        CropX.ias.setSelection(
            x,
            y,
            x + roundCoord(se.width),
            y + roundCoord(se.height)
        );
        CropX.ias.update();
        CropX.notify(CropX.i8n.centered, 'success');
    };

    // =========================================================================
    // SURCHARGE DE contract - RESET ZOOM PROPRE
    // =========================================================================

    CropX.contract = function(that) {
        var data = CropX.getData($(that).closest('#toolbar-crop'));
        data['selection'] = CropX.ias.getSelection();

        $.ajax({
            cache: false,
            url: CropX.link + '&t=setContract',
            data: { data: data },
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                CropX.addProgress();
            },
            success: function(response) {
                CropX.ias.setSelection(
                    roundCoord(response.x),
                    roundCoord(response.y),
                    roundCoord(response.x + response.width),
                    roundCoord(response.y + response.height)
                );
                CropX.ias.setOptions({ fadeSpeed: 1000, show: true });
                CropX.ias.update();

                if (CropX.btnZoomRange) {
                    CropX.btnZoomRange.value = 1;
                }
                if (CropX.panzoom) {
                    CropX.panzoom.zoom(1);
                }

                CropX.removeProgress();
                CropX.notify(CropX.i8n.contracted, 'success');
            },
            error: function() {
                CropX.removeProgress();
            }
        });
    };

    console.log('CropX Patch v1.1.0 loaded - Calculation fixes applied');

})(jQuery);
