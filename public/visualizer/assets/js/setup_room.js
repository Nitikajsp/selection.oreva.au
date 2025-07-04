get_url_params = function (index, qd) {
    qd = qd || {};
    if (location.search) location.search.substr(1).split("&").forEach(function (item) {
        var s = item.split("="),
            k = s[0],
            v = s[1] && decodeURIComponent(s[1]); //  null-coalescing / short-circuit
        //(k in qd) ? qd[k].push(v) : qd[k] = [v]
        (qd[k] = qd[k] || []).push(v) // null-coalescing / short-circuit
    })
    if ( index ) {
        return qd[index]
    }
    return qd;
}
let room_id = makeSingle(get_url_params('room_id') || 'bathroom_large');
let room_params = {}
room_params.preset = get_url_params('presets');
let sprites = {};
let selection = {};
let selected_meshes = {};
let chat_temporary_data = {};

(function ($) {
    var timeout;
    $(document).on('mousemove', function (event) {
        if (timeout !== undefined) {
            window.clearTimeout(timeout);
        }
        timeout = window.setTimeout(function () {
            // trigger the new event on event.target, so that it can bubble appropriately
            $(event.target).trigger('mousemoveend');
        }, 100);
    });
}(jQuery));

function get_sprite_size() {
    return Math.max((1024 - window.outerWidth)/14, 0) + 3;
}

function isSupportPDF() {
    if ( navigator.pdfViewerEnabled ) {
        return true;
    }
    var hasPDFViewer = false;
    try {
        var pdf =
            navigator.mimeTypes &&
            navigator.mimeTypes["application/pdf"]
                ? navigator.mimeTypes["application/pdf"].enabledPlugin
                : 0;
        if (pdf) { return true; };
    } catch (e) {
        if (navigator.mimeTypes["application/pdf"] != undefined) {
            return true;
        }
    }
    return false;
}

function getTagElementPosition(elem, x, y, dist) {
    width = $( elem ).width();
    height = $( elem ).height();
    dist = dist || 20;
    if ( x < $( window ).width()/2 ) {
        if ( y < $( window ).height()/2 ) {
            return [x + dist, y + dist]
        } else {
            return [x + dist, y - height - dist]
        }
    } else {
        if ( y < $( window ).height()/2 ) {
            return [x - width - dist, y + dist]
        } else {
            return [x - width - dist, y - height - dist]
        }
    }
}
function positionTagElement(elem, x, y, dist) {
    let [lft, tp] = getTagElementPosition(elem, x, y, dist);
    $( elem ).animate({'left': lft, 'top': tp}, "fast");
    $( elem ).show();
}

$( document ).ready(function() { 
    initFunction('renderCanvas', room_id, room_params, function(scene) {
        let s = window.p.data.variable_meshes;
        for ( const [k, g] of Object.entries(s) ) {
            console.log("Loading variable surface", g.default)
            let m = scene.getMeshByName(room_params.presets ? (g.preset_default || g.default) : g.default)
            m.name = k;
            if (!g.only_in_presets) {
                m.isPickable = true;
                m.is_pickable = true;
                sprites[k] = g
                sprites[k].id = k;
                window.p.setSprite(k, g.surface_points, g.size);
            }
        }
        var timer;
        $( "#renderCanvas" ).on('mousemove keypress touchstart', function(){
            window.p.pickMesh(function(name) {
                console.debug("Picked mesh: " +  name);
                clearTimeout(timer);
                window.p.showSprites();
                timer = setTimeout(function(){
                    window.p.hideSprites();
                }, 1500);
            }, function() {
                console.debug("Not hit any pickable mesh");
            });
        });
        $( "#renderCanvas" ).on('mousemoveend touchend', function(){
            window.p.pickMesh(function(name) {
                let [x,y] = window.p.getScreenCoordinates(window.p.getSprite(name).position);
                console.debug( "Position of tag element will be: " +  x + " , " + y);
                let product_data = getProductData(name); 
                let features = product_data.product_features; 

                let featuresHtml = ''; 
                if(features.length){
                    features.map((feature)=>{ 
                        featuresHtml = featuresHtml + `<li>${feature}</li>`; 
                    })
                } 
                if(featuresHtml){
                    featuresHtml = `<ul> ${featuresHtml}</ul>`;
                }
                let hoverDesign = `
                    <a class="close-popup-tag" href="javascript:void(0)"><img src="assets/img/newui/close.svg" style="width:22px"/></a>
                    <div class="mds-popup-block">
                        <div class="mds-popup-media">
                            <img src="${product_data.application_image}"/>
                        </div>               
                        <div class="mds-popup-description">
                            <h6><b>${firstUpperCase(product_data.category_name) + ' - ' + firstUpperCase(product_data.product_name)}</b></h6>
                            ${featuresHtml}
                        <div>  
                    <div>  `;
                $( "#popup-tag" ).html( hoverDesign );

                $(".close-popup-tag").on("click", function(){ 
                    $( "#popup-tag" ).hide();
                })

                //$( "#popup-tag" ).text( product_data.category_name + ' - ' + product_data.product_name );
                positionTagElement("#popup-tag", x, y);
            }, function() {
                $( "#popup-tag" ).hide();
            });
        });

        

        $( '#renderCanvas').on("click", function() {
            
            window.p.pickMesh(function(name) {
                console.debug("Picked mesh: " +  name);
                //getProductCategories(name)
            }, function() {
                console.debug("Not hit any pickable mesh");
            });
            window.p.pickSprite(function(name) {
                console.log("Picked sprite: " +  name);
                getProductCategories(name);
                $( ".nav-open .downbtn" ).click();
            }, function() {
                console.debug("Not hit any sprite");
            });
        });
        apply_selected_from_url();
        if ( window.scene_loaded && typeof ( window.scene_loaded ) == 'function' ) {
            window.scene_loaded();
        } 
    }).then((p) => {
        window.p = p;
        p.sceneToRender = p.scene;
    });
    $('#startbtn').on('click', function() {
        $('#startcover').toggleClass('hidesection');
    });
});

function firstUpperCase(string){ 
    return string.replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
}

function post_interaction(params, stage, rid, callbackFunc, errorFunc) {
    rid = rid || room_id;
    params = params || {};
    stage = stage || ( params['category_name'] ? 'viewed' : 'visited');
    let HEADERS = getCookie('authentication');
    params["customer_id"] = getCookie('customer_id');
    params["room_id"] = rid;
    chat_temporary_data['room_id'] = rid;
    chat_temporary_data['room_name'] = categories['name'] || rid;
    params["stage"] = stage;
    params["_async"] = (stage == 'shortlisted' ? false : true);
    if ( params.category_name && params.product_id ) {
        selection[params.category_id] = params;
        selected_meshes[params.category_id] = params.mesh_id;
    }
    for (let c of ['category_name', 'product_name', 'category_id', 'product_id']) {
        if ( params[c] ) {
            chat_temporary_data[c] = params[c];
        }
    }
    ajaxRequestWithData(
        dave_url + "/object/interaction",
        'POST', HEADERS, JSON.stringify(params), callbackFunc, errorFunc
    )
    if ( typeof(gtag) != 'undefined' ) {
        gtag('event', 'click', {
            'room_id': room_id,
            'event_category': 'product_view--' + rid,
            'event_label': rid + '--' + (params.category_name || '') + '--' + (params.product_id || '')
        });
    }
}
function get_product_wishlist(callbackFunc, errorFunc) {
    let rid = room_id;
    let stage = 'shortlisted'
    let HEADERS = getCookie('authentication');
    params = {
        "customer_id": getCookie('customer_id'),
        "room_id": rid,
        "stage": stage,
        "_action": "object_list",
        "_over": "category_name",
        "_fresh": true
    }
    ajaxRequestWithData(
        dave_url + "/pivot/interaction/category_name",
        'GET', HEADERS, params, callbackFunc, errorFunc
    )
}

function add_product_wishlist(params, callbackFunc, errorFunc) {
    post_interaction(params, 'shortlisted', callbackFunc, errorFunc);
}

function remove_product_wishlist(interaction_id, callbackFunc, errorFunc) {
    let HEADERS = getCookie('authentication');
    ajaxRequestWithData(
        dave_url + "/object/interaction/" + interaction_id,
        'DELETE', HEADERS, JSON.stringify({}), callbackFunc, errorFunc
    )
}
function add_room_wishlist(callbackFunc, errorFunc) {
    let HEADERS = getCookie('authentication');
    let rid = room_id;
    take_snapshots(function(img) {;
        ajaxRequestWithData(
            dave_url + "/object/wishlist",
            'POST', HEADERS, JSON.stringify({
                "room_id": rid,
                "selection": selection,
                "customer_id": getCookie('customer_id')
            }), callbackFunc, errorFunc
        )
    });
}
function get_room_wishlist(callbackFunc, errorFunc) {
    let HEADERS = getCookie('authentication');
    let rid = room_id;
    ajaxRequestWithData(
        dave_url + "/objects/wishlist",
        'GET', HEADERS, {
            "room_id": rid,
        }, callbackFunc, errorFunc
    )
}

function delete_room_wishlist(wishlist_id, callbackFunc, errorFunc) {
    let HEADERS = getCookie('authentication');
    ajaxRequestWithData(
        dave_url + "/object/wishlist/" + wishlist_id,
        'DELETE', HEADERS, JSON.stringify({}), callbackFunc, errorFunc
    )
}

function apply_room_wishlist(sel, callbackFunc, errorFunc) {
    selc = Object.assign({}, (sel || selection));
    for (let k in sel) {
        let s = selc[k];
        p.switchMeshes(k, k+"/"+s.mesh_id);
    }
}

function apply_selected(sel, callbackFunc, errorFunc) {
    selc = Object.assign({}, (sel || selected_meshes));
    let n = 0;
    for (let k in sel) {
        let s = selc[k];
        if ( k != 'presets') {
            n++;
            if ( n >= sel.length ) {
                p.switchMeshes(k, k+"/"+s, callbackFunc);
            } else {
                p.switchMeshes(k, k+"/"+s);
            }
        }
    }
}

function apply_selected_from_url(callbackFunc, errorFunc) {
    sel = get_url_params();
    delete sel.room_id;
    apply_selected(sel, callbackFunc, errorFunc);
}

function create_copy_url() {
    let url = document.location.href
    if (window.URLSearchParams !== undefined ) {
        var searchParams = new URLSearchParams(window.location.search)
        Object.entries(selected_meshes).map(function(k, v) {
            searchParams.set(k[0], k[1]);
            return true;
        });
        url = document.location.origin + window.location.pathname + '?' + searchParams.toString();
    }
    return url
}

