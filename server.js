let express = require('express');
let http = require('http');
let path = require('path');
let app = express();
let mysql=require('mysql');
app.set('view engine','ejs');
let server = http.createServer(app);
let db=mysql.createConnection({host:'localhost',user:'root',password:'',database:'products'});
app.use('/images', express.static(path.join(__dirname, 'public/images')));
app.use('/assets', express.static(path.join(__dirname, 'public/assets')));
app.use('/css', express.static(path.join(__dirname, 'public/css')));
app.use('/admin', express.static(path.join(__dirname, 'public/admin')));
app.use(express.static(path.join(__dirname, 'views')));

const loadProducts = async(length) =>{
    return new Promise((resolve,reject)=>{
        db.query('SELECT prod_id,prod_name,prod_primary_color , prod_slab_size1 , prod_slab_size2 ,prod_stone_cat , prod_finishes ,prod_images From products_data WHERE prod_images IS NOT NULL AND prod_status!=1 ORDER BY created_date DESC limit ?' , [length] , async(err,res)=>{
            if(err){
                reject(err);
            }else{
               resolve(res);
            }
        })
    })

}
const filters = async() =>{
    return new Promise((resolve,reject)=>{
        db.query("SELECT GROUP_CONCAT(DISTINCT CONCAT(prod_primary_color) SEPARATOR ',' ) as prod_primary_color , GROUP_CONCAT(DISTINCT CONCAT(prod_finishes) SEPARATOR ',' ) as prod_finishes, GROUP_CONCAT(DISTINCT CONCAT(prod_stone_cat) SEPARATOR ',' ) as prod_stone_cat , GROUP_CONCAT(DISTINCT CONCAT(prod_slab_size1, ',', prod_slab_size2) SEPARATOR ',' ) as sizes FROM `products_data` WHERE prod_images IS NOT NULL AND prod_status!=1 AND prod_images IS NOT NULL AND prod_status!=1 AND prod_slab_size1!=' ' AND prod_slab_size2!=' ' AND prod_slab_size1!='.' AND prod_slab_size2!='.'" , [] , async(err,res)=>{
            if(err){
                reject(err);
            }else{
               resolve(res[0]);
            }
        })
    })

}

app.get('/products',async(req,res)=>{

    console.log(await loadProducts(18));
    console.log(await filters());

    let loadProds=await loadProducts(18);
    let filter=await filters();

    res.render('products',{loadProds:loadProds,filter:filter})


})

server.listen(9595,()=>{
    console.log('serve running with port 9595');
});