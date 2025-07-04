//Get Main product categories
let categories = {};
const getProductCategories = async (categoryId) => {
    try {
        if ( $.isEmptyObject(categories) ) {
            let productCategories = await fetch("assets/json/product_categorys.json"); 
            categories = await productCategories.json(); 
        }
        if(categoryId && !presets.length){
            const singleCategory = categories.find((category)=>{  
                if ( categoryId==category.category_id ) {
                    post_interaction({category: category.category_name, category_id: category.category_id});
                    return true
                }
                return false
            }); 
            setProducts(singleCategory) 
        }else if (!presets.length){
            setProductCategories(categories) 
        } else if (categoryId && presets.length ) {
            window.p.focusOnMesh(categoryId, sprites[categoryId].position, sprites[categoryId].target || sprites[categoryId].surface_points);
        }
        if ( window.ui_loaded && typeof ( window.ui_loaded ) == 'function' ) {
            window.ui_loaded();
        }
    } catch (error) {
        console.log("Error : ", error);
    }
}

let presets = {};
let current_budget_preset_id = null;
let current_budget_preset_products = null;
const getBudgetPresets = async () => {
    try {
        if ( $.isEmptyObject(presets) ) {
            let productCategories = await fetch("assets/json/budget_products.json"); 
            presets = await productCategories.json(); 
        }
        setBudgetPresets(presets);
        if ( window.ui_loaded && typeof ( window.ui_loaded ) == 'function' ) {
            window.ui_loaded();
        }
    } catch (error) {
        console.log("Error : ", error);
    }
}
const getBudgetProduct = async (budget_preset) => {
    try {
        const singleCategory = presets.find((category)=>{  
            if ( budget_preset == category.budget_preset_id ) {
                current_budget_preset_id = budget_preset;
                current_budget_preset_products = category.products;
                post_interaction({budget_preset: category.budget_preset});
                return true
            }
            return false
        });
        setBudgetProduct(singleCategory);
        if ( window.ui_loaded && typeof ( window.ui_loaded ) == 'function' ) {
            window.ui_loaded();
        }
    } catch (error) {
        console.log("Error : ", error);
    }
}
//Set category products
const setProducts = (category)=>{
    const products = category.products;
    const categoryName = category.category_name.toUpperCase();
    let singleProduct = "";
    $("#show").html(""); 
    $(".categoriesBtn").hide();  
    
    $('#popup-tag').hide();
    $(".productsBtn").show().html(`<i class="fa fa-chevron-left mr-10" aria-hidden="true"></i> ${categoryName}`).prop('title', categoryName);
    toTopCategories()
    window.p.focusOnMesh(category.category_id, sprites[category.category_id].position, sprites[category.category_id].target || sprites[category.category_id].surface_points);
    if(products){
        products.map((product)=>{
            if ( ( !product.available_in ) || product.available_in.indexOf(room_id) >= 0 ) { 
                singleProduct = $(`
                <a href="javascript:void(0)" class="product singleProduct gatrack" data-product_id="${product.product_id}" data-mesh_id="${product.mesh_id}" data-category_id="${category.category_id}" data-ga="${category.category_name}--${product.product_name}">
                    <div class="thumbnail-image thumbnail-image11 ${(product.product_price != 'none' || product.product_price != 'none') ? "thumbnail-image12" : "noibtn"}">
                    <div  style="${(product.product_price != 'none' || product.product_price != 'none') ? "" : "display:none;"}">
                    <div class="ibtn ibtn2" > <img class="ibtn2" src="assets/img/newui/Group.svg" alt="slider-img" id="itbn12312"></div>
                    </div>
                        <div class="thumbImg"   style="${(product.product_amazon === 'coming soon..' && product.product_yt === 'coming soon..') ? "max-height: 181px !important;" : ""}">
                            <img src="${product.application_image}" alt="slider-img" class="img121">
                            <div class="product-details" style="display:none;">
                            <p class="ibtnsku ibtnname">${product.product_name}</p>
                            <p class="ibtnprice">₹ ${product.product_price}</p>
                            <p class="ibtnsku">SKU: ${product.product_sku}</p>
                            ${product.product_amazon === "coming soon.." ? '' : `<img class="ibtnamzimg" onclick="window.open('${product.product_amazon}', '_blank').focus()" src="assets/img/ibtn/ibtnamz.png" alt="" style="${(product.product_yt === 'coming soon..') ? "left: 24px !important;" : ""}">`}
                            ${product.product_yt === "coming soon.." ? '' : `<img class="ibtnytimg" onclick="window.open('${product.product_yt}', '_blank').focus()" src="assets/img/ibtn/ibtnyt.png" alt="" style="${(product.product_amazon === 'coming soon..') ? "left: 24px !important;" : ""}">`}
                             
                            </div>
                        </div>
                        <span class="hovertohide" >${product.product_name}</span>
                        <span class="hovertoshow" style="${(product.product_price != 'none' || product.product_price != 'none') ? "" : "display:none;"}">MRP: ₹ ${product.product_price}</span>
                        <span class="hovertoshow" style="${(product.product_sku !== undefined && product.product_price == 'none' && (product.product_sku != 'none' || product.product_price != 'none')) ? "" : "display:none;"}">SKU : ${product.product_sku}</span>
                    </div>
                </a>
                `);
                singleProduct.data('product', product);
                singleProduct.data('category', category);
                $("#show").append(singleProduct); 
            }
        });

        let noResult = `
            <a href="javascript:void(0)" class="no-result" style="display:none">
                <div class="thumbnail-image"> 
                    <p class="budget">No Results Found</p>
                </div>
            </a>`;
        $("#show").append(noResult);

    }
    // style="${(product.product_amazon === 'coming soon..' && product.product_yt === 'coming soon..') ? "top: -79px !important;" : ""}"

    $(".singleProduct").on("click", function(){
        const categoryId = $(this).data("category_id");
        const mesh_id = $(this).data("mesh_id");
        const category = $( this ).data('category');
        const product = $( this ).data('product');
        window.p.focusOnMesh(category.category_id, sprites[category.category_id].position, sprites[category.category_id].target || sprites[category.category_id].surface_points);
        p.switchMeshes(categoryId,categoryId+"/"+mesh_id, $(this).data('product'))
        post_interaction(product);
        $( "#share2" ).addClass('share-show');
        console.log(`${categoryId} ${mesh_id}`)
        $('#popup-tag').hide();
    });
    // Note to Aryan (for adding product to wishlist, here is how to do it)
    $( ".wishlist" ).on("click", function() {
        add_product_wishlist($( this ).parents(".singleProduct").data('product'));
    });
}


$('#proddesc').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('price')
    var prodimg = button.data('prodimg')
    var prodname = button.data('whatever')
    var sku = button.data('sku')

    var amzlink = button.data('amzlink') 
    var ytlink = button.data('ytlink') 

     // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    var modal = $(this)
    // modal.find('.itemname2').text(recipient + '- Video')
    // modal.find('.itemname').text(recipient)
    document.getElementById("prodpic").src = prodimg;
    // document.getElementById("prodpic2").src = prodimg;
    if (ytlink.startsWith('http') ) {
        document.getElementById("ytbtndiv").style.display = "";;

        document.getElementById("ytvid1").href = ytlink;
        document.getElementById("ytbtn").href = ytlink;

        $("#ytvid1").children().text('Watch Product Video')
    } else {
        document.getElementById("ytbtndiv").style.display = "none";;
        
        document.getElementById("ytvid1").href = "#";
        document.getElementById("ytbtn").href = "#";

        $("#ytvid1").children().text('Video Coming Soon..');
        $("#ytvid1").prop('target', '_self');
        $("#ytbtn").prop('target', '_self');
    }
    if (amzlink.startsWith('http') ) {
        document.getElementById("amzbtndiv").style.display = "";;

        document.getElementById("amzlink").href = amzlink;
        document.getElementById("amzbtn").href = amzlink;
        
        $("#amzlink").children().text('Buy on Amazon')

    } else {
        document.getElementById("amzbtndiv").style.display = "none";;

        $("#amzlink").children().text('Coming Soon')
        document.getElementById("amzlink").href = "#";
        document.getElementById("amzbtn").href = "#";

        $("#amzlink").prop('target', '_self');
        $("#amzbtn").prop('target', '_self');

    }

    // document.getElementById("ytvid1").href = ytlink;

    
    modal.find('.prodname').text(prodname)
    modal.find('.skuid').text('SKU:'+sku)
   
    modal.find('.amazonlink').text('MRP:₹'+recipient)
    // modal.find('.ytlink').text(ytlink)


    
  })


//Set Main product categories
const setProductCategories = (categories)=>{
    let singleCategory = "";
    $("#show").html("");
    $(".categoriesBtn").show();  
    $(".productsBtn").hide();
    $('#popup-tag').hide();
    toTopCategories()
    if(categories){
        categories.map((category)=>{
            if ( ( !category.available_in ) || category.available_in.indexOf(room_id) >= 0 ) { 
                singleCategory = $(`
                <a href="javascript:void(0)" class="mainCategory" data-category_id="${category.category_id}">
                    <div class="thumbnail-image">
                        <div class="thumbImg"> 
                            <img src="${category.application_image}" alt="slider-img">
                        </div>
                        <span>${category.category_name.toUpperCase()}</span>
                    </div>
                </a>`);
                singleCategory.data('category', category);
                $("#show").append(singleCategory);
            }
        }); 
        let noResult = `
            <a href="javascript:void(0)" class="no-result" style="display:none">
                <div class="thumbnail-image"> 
                    <p class="budget">No Results Found</p>
                </div>
            </a>`;
        $("#show").append(noResult);
    }

    //click  to get category products
    $(".mainCategory").on("click", function(){
        const categoryId = $(this).data("category_id");
        getProductCategories(categoryId)
    })  

    //Go back to categories 
    $(".productsBtn").on("click", function(){
        getProductCategories();
    }) 
}

const setBudgetPresets = (categories)=>{
    let singleCategory = "";
    $("#show").html("");
    $(".categoriesBtn").show();  
    $(".productsBtn").hide();
    $('#popup-tag').hide();
    toTopCategories()
    if(categories){
        categories.map((category)=>{
            if ( ( !category.available_in ) || category.available_in.indexOf(room_id) >= 0 ) { 
                singleCategory = $(`
                <a href="javascript:void(0)" title="${category.budget_preset.toUpperCase()}" class="mainCategory" data-category_id="${category.budget_preset_id}">
                    <div class="thumbnail-image"> 
                         <div class="thumbImg"> 
                            <img src="${category.application_image}" alt="${category.budget_preset.toUpperCase()}">
                        </div>
                        <p class="budget" title="${category.budget_preset.toUpperCase()}">${category.budget_preset.toUpperCase()}</p>
                    </div>
                </a>`);
                singleCategory.data('category', category);
                $("#show").append(singleCategory);
            }
        }); 
    }

    //click  to get category products
    $(".mainCategory").on("click", function(){
        const categoryId = $(this).data("category_id");
        getBudgetProduct(categoryId)
    })  

    //Go back to categories 
    $(".productsBtn").on("click", function(){
        getBudgetPresets();
    }) 
}

const setBudgetProduct = (category)=>{
    const products = category.products;
    const categoryName = category.budget_preset.toUpperCase();
    let singleProduct = "";
    $("#show").html(""); 
    $(".categoriesBtn").hide();  
    $('#popup-tag').hide();
    $(".productsBtn").show().html(`<i class="fa fa-chevron-left mr-10" aria-hidden="true"></i> ${categoryName}`).prop('title', categoryName);
     toTopCategories();
    if(products){ 
        products.map((product)=>{
            let cid = product.aligned_category_id || product.category_id;
            if ( ( !product.available_in ) || product.available_in.indexOf(room_id) >= 0 ) { 
                singleProduct = $(`  
                <a href="javascript:void(0)" class="product singleProduct gatrack" data-product_id="${product.product_id}" data-mesh_id="${product.mesh_id}" data-category_id="${cid}" data-ga="${category.budget_preset}--${product.category_name}--${product.product_name}">
                    <div class="thumbnail-image thumbnail-image11 ${(product.product_price != 'none' || product.product_price != 'none') ? "thumbnail-image12" : "noibtn"}">
                    <div  style="${(product.product_price != 'none' || product.product_price != 'none') ? "" : "display:none;"}">
                    <div class="ibtn ibtn2" > <img class="ibtn2" src="assets/img/newui/Group.svg" alt="slider-img"></div>
                    </div>
                        <div class="thumbImg"   style="${(product.product_amazon === 'coming soon..' && product.product_yt === 'coming soon..') ? "max-height: 181px !important;" : ""}">
                            <img src="${product.application_image}" alt="slider-img" class="img121">
                            <div class="product-details" style="display:none;">
                            <p class="ibtnsku ibtnname">${product.product_name}</p>
                            <p class="ibtnprice">₹ ${product.product_price}</p>
                            <p class="ibtnsku">SKU: ${product.product_sku}</p>
                            ${product.product_amazon === "coming soon.." ? '' : `<img class="ibtnamzimg" onclick="window.open('${product.product_amazon}', '_blank').focus()" src="assets/img/ibtn/ibtnamz.png" alt="" style="${(product.product_yt === 'coming soon..') ? "left: 24px !important;" : ""}">`}
                            ${product.product_yt === "coming soon.." ? '' : `<img class="ibtnytimg" onclick="window.open('${product.product_yt}', '_blank').focus()" src="assets/img/ibtn/ibtnyt.png" alt="" style="${(product.product_amazon === 'coming soon..') ? "left: 24px !important;" : ""}">`}
                            </div>
                        </div>
                        <span class="hovertohide" >${product.product_name}</span>
                        <span class="hovertoshow" style="${(product.product_price != 'none' || product.product_price != 'none') ? "" : "display:none;"}">MRP: ₹ ${product.product_price}</span>
                        <span class="hovertoshow" style="${(product.product_sku !== undefined && product.product_price == 'none' && (product.product_sku != 'none' || product.product_price != 'none')) ? "" : "display:none;"}">SKU : ${product.product_sku}</span>
                    </div>
                </a>`);
                if ( product.mesh_id ) {
                    setTimeout(function() {
                        p.switchMeshes(cid, cid+"/"+product.mesh_id, product);
                        post_interaction(product);
                    }, 100);
                } 
                singleProduct.data('product', product);
                singleProduct.data('category', {'category_id': product.aligned_category_id, 'budget_preset': category.budget_preset, 'products': products });
                $("#show").append(singleProduct);
            }
        });
        let noResult = `
            <a href="javascript:void(0)" class="no-result" style="display:none">
                <div class="thumbnail-image"> 
                    <p class="budget">No Results Found</p>
                </div>
            </a>`;
        $("#show").append(noResult);
    }
    
    $(".singleProduct").on("click", function(){
        const categoryId = $( this ).data("category_id");
        const category = $( this ).data('category');
        const product = $( this ).data('product');
        window.p.focusOnMesh(categoryId, sprites[categoryId].position, sprites[categoryId].target || sprites[categoryId].surface_points);
        post_interaction(product);
        $( "#share2" ).addClass('share-show');
        $('#popup-tag').hide();
    });

    // Note to Aryan (for adding product to wishlist, here is how to do it)
    $( ".wishlist" ).on("click", function() {
        add_product_wishlist($( this ).parents(".singleProduct").data('product'));
    });
}

//Scroll to(show div) top
const toTopCategories = ()=>{
    $('.banner-slider .navchild').animate({ scrollTop: 0 }, 100);
}
  
$(()=>{ 
	getCookie('authentication') || signup(); 
    //call main categories
    params = get_url_params()
    if ( params.presets ) {
        getBudgetPresets();
    } else {
        getProductCategories();
    }
    post_interaction();
})

$( window ).on('load', function() {
    /mobile/i.test(navigator.userAgent) && !location.hash && setTimeout(function() {
        console.log("Scrolling now");
        // window.scrollTo(0, 1);
    }, 1000);
});




 

      











