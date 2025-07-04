function fetchPDFContent() {
    return new Promise(function (resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', './pdf.html');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    resolve(xhr.responseText);
                } else {
                    reject(xhr.statusText);
                }
            }
        };
        xhr.send();
    });
}

function recurseCameras(position_list, after_click_func, final_func, width, height, seq) {
    // after_click_func takes in the index of the click and data of the camera click;
    // final_func does not take any params
    seq = seq || 0;
    width = width || 720;
    height = height || 360;
    if ( position_list.length <= 0 ) {
        p.resetCamera();
        final_func();
        return;
    }
    let c = position_list[0];
    position_list = position_list.slice(1);
    if ( c.skip_pdf ) {
        recurseCameras(position_list, after_click_func, final_func, width, height, seq + 1);
        return;
    }
    p.resetCamera(c.position, c.target, c.fov);
    setTimeout(function() {
        if (!window.OffscreenCanvas) {
            BABYLON.Tools.CreateScreenshotUsingRenderTarget(p.engine, p.camera, {width: c.width || width, height: c.height || height}, function(data) {
                after_click_func(c.sequence || seq, data, seq);
                setTimeout(function() {
                    recurseCameras(position_list, after_click_func, final_func, width, height, seq + 1);
                    return;
                }, 500);
            },  'image/jpeg', null, true);
        } else {
            BABYLON.Tools.CreateScreenshot(p.engine, p.camera, {width: c.width || width, height: c.height || height}, function(data) {
                after_click_func(c.sequence || seq, data, seq);
                setTimeout(function() {
                    recurseCameras(position_list, after_click_func, final_func, width, height, seq + 1);
                    return;
                }, 500);
            },  'image/jpeg', null, true);
        }
    }, 500);
}

function canonicalize(url) {
    var div = document.createElement('div');
    div.innerHTML = "<a></a>";
    div.firstChild.href = url; // Ensures that the href is properly escaped
    div.innerHTML = div.innerHTML; // Run the current innerHTML back through the parser
    let r = div.firstChild.href;
    div.remove()
    return r;
}

let getBase64FromUrl = async (url) => {
  url = canonicalize(url);
  const data = await fetch(url, {mode: 'cors'});
  const blob = await data.blob();
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.readAsDataURL(blob); 
    reader.onloadend = () => {
      const base64data = reader.result;   
      resolve(base64data);
    }
  }); 
}

function getProductData(surface) {
    if ($.type(surface) == 'object') {
        return surface;
    }
    
    console.debug("Getting surface category ", surface);
    let sf = $("[data-category_id=" + surface + "]").data('category');
    
    if (sf) {
        sf = sf['products'];
    } else {
        sf = categories.filter((_) => { return _.category_id == surface });
        if (sf.length) {
            sf = sf[0].products;
        }
    }

    // Get the product ID from selected_meshes or default
    let pid = selected_meshes[surface] || p.data.variable_meshes[surface]['default'];

    // Apply sel logic only if using the default `pid`
    if (!selected_meshes[surface]) {
        // Get the URL parameters, and remove room_id if present
        let sel = get_url_params();
        delete sel.room_id;

        // Check if `sel` contains a product for the current surface (e.g., water_closet=cora_vortex)
        if (sel[surface]) {
            // If the selected product exists in the URL parameters, use it instead of `pid`
            pid = sel[surface];
        }
    }

    // Iterate through the product list to find the product by `product_id` or `mesh_id`
    for (let k of sf) {
        if (k['product_id'] == pid || k['mesh_id'] == pid) {
            return k; // Return the matched product
        }
    }

    return; // Return undefined if no product matches
}


function displayPDFContent() { 
    if (!presets.length) {
        $( ".productsBtn" ).click();
    }
    $( "#loader_pdf" ).show();
    p.hideSprites();
    fetchPDFContent()
        .then(function (content) {
             // Add the content to your modal
             var modal = document.getElementById('modal');
             if (modal) {
                 $( "#pdf_modal" ).empty().append(content)
             } else {
                 console.error('Modal element not found');
                 return
             }
            content = $( "#pdf_modal" )
            // go through the pdf_cameras and take pictures
            let def_pdf_cameras = [
                {
                    target: p.data.camera_target,
                    position: p.data.camera_position
                },
                {
                    target: p.data.camera_target,
                    position: p.data.camera_position
                },
            ]
            if (p.data.room_type) {
                content.find(".pdf_logo").attr("src", "assets/img/" + p.data.room_type.toLowerCase() + "_logo.png");
            }
            
            content.find(".pdf_title").text( p.data.name.toUpperCase()  + ' BATHROOM');
            let counter = 1;
            recurseCameras((p.data.pdf_cameras || def_pdf_cameras).slice(), function(index, data) {
                content.find( "#pdf_camera_" + (index+1) ).attr('src', data);
            }, function() {
                let variable_surfaces = Object.entries(p.data.variable_meshes).map(function(v) { return {target: v[1].target, position: v[1].position, sequence: v[0], skip_pdf: v[1].skip_pdf, fov: v[1].pdf_fov}});
                content.find( "#pdf_products_1, #pdf_products_2" ).empty();
               
                recurseCameras(variable_surfaces, function(surface, data, index) { 
                  
                    console.log("surface : " + surface)
                    let pd = getProductData(surface);
                    if ( !pd ) {
                        console.error("Did not find product data for ", surface);
                        return
                    }
                    let pdc = `
                        <div class="col-4 mds-prod sel-prod">
                            <img src="${data}" class=""> 
                            <span>${pd.product_name.toUpperCase()}</span>
                            <p class="mds_sku">SKU : ${pd.product_sku}</p>
                        </div>
                    `
                    if(index<=2){
                        content.find( "#pdf_products_1" ).append(pdc);
                    }else{
                        content.find( "#pdf_products_2" ).append(pdc);
                    }
                }, function() {
                   
                    content.find( "#pdf_product_details" ).empty();
                    f = function(meshes, final_function) {
                        if (meshes.length <= 0 ) {
                            final_function();
                            return
                        }
                        let surface = meshes.shift()
                        let pd = getProductData(surface);
 
                        if ( !pd ) {
                            console.error("Did not find product data for ", surface);
                            f(meshes, final_function);
                            return
                        }
                        if ( !pd.product_sku ) {
                            console.info("Not an hindware product for ", pd.product_name, ' in ', surface);
                            f(meshes, final_function);
                            return
                        }
                        console.log("Trying to get", pd.application_image);
                        let f1 = function(data) {
                            let pdc = `
                                <div class="col-4 prodpad">
                                    <div class="prod-div">
                                        <div class="col-12 prod-img">
                                            <img src="${data}" alt="">
                                        </div> 
                                        <div class="col-12 prod-descq"  style="${(pd.product_price != 'none') ? "" : ""}"  >
                                            <span>${pd.product_name.toUpperCase()}</span>
                                            <p class="sku">SKU : ${pd.product_sku}</p>
                                            <p class="mrp" style="${(pd.product_price != 'none') ? "" : "display:none;"}"> MRP : ${pd.product_price}</p>
                                        </div> 
                                    </div>
                                </div>
                            `
                            console.log("counter : " + counter);
                            content.find( "#pdf_product_details" ).append(pdc)
                            // if(counter<=6){
                            //     content.find( "#pdf_product_details" ).append(pdc)
                            // }else{
                            //     $("#pdf_page4").show
                            //     content.find( "#pdf_product_details_2" ).append(pdc)
                            // }

                            
                            counter++;
                            f(meshes, final_function);
                        }
                        getBase64FromUrl(pd.application_image).then(f1);
                    }
                    f(current_budget_preset_products ? current_budget_preset_products :Object.keys(p.data.variable_meshes), function() {
                        $( "#modal" ).show();
                        $( "#loader_pdf" ).hide();
                        let doc;
                        setTimeout(function() {
                            doc = generatePDF();
                            $( "#pdf_download" ).on("click", function() {
                                doc.save("selected_product_" + p.room_id + ".pdf")
                            });
                        }, 1);
                    });
                }, 516, 600);
            });
        })
        .catch(function (error) {
            console.error('Error fetching PDF content:', error);
        });
}

function generatePDF() {
    window.jsPDF = window.jspdf.jsPDF;

    // Create a new jsPDF instance
    const doc = new jsPDF({ unit: 'pt', format: 'a4', orientation: 'portrait' });

    // Define the page size and orientation
    const width = doc.internal.pageSize.getWidth();
    const height = doc.internal.pageSize.getHeight();
    console.log(height,doc,doc.internal,doc.pageSize)
    // Generate the PDF for each page
    html2canvas(document.getElementById("pdf_page1"),{
        scale: 2,
        dpi: 700,
        width: width,
        height: height
    }).then(canvas => {
        doc.addImage(canvas.toDataURL(), "PNG", 0, 0, width, height);
        doc.addPage();
        html2canvas(document.getElementById("pdf_page2"),{
            scale: 2,
            dpi:700,
            width: width,
            height: height
        }).then(canvas => {
            doc.addImage(canvas.toDataURL(), "PNG", 0, 0, width, height);
            doc.addPage();
            html2canvas(document.getElementById("pdf_page3"),{
                scale: 2,
                dpi:700,
                width: width,
                height: height
            }).then(canvas => {
                doc.addImage(canvas.toDataURL(), "PNG", 0, 0, width, height);
                if ( typeof (p) != 'undefined' && p.room_id ) {
                    doc.save("selected_products_" + p.data.name + ".pdf");
                } else {
                    doc.save("selected_products.pdf");
                }
            });
        });
    });
    return doc
}
