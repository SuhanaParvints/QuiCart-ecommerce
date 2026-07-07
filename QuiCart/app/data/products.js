const img = (id) => `https://images.unsplash.com/photo-${id}?w=900`

export const products = {
  // WOMEN
  dresses: [
    { id: 1, name: 'Floral Maxi Dress', price: 2499, rating: 4.8, stock: 24, image: img('1515372039744-b8f02a3ae446') },
    { id: 2, name: 'Elegant Midi Dress', price: 2299, rating: 4.7, stock: 18, image: img('1496747611176-843222e1e57c') },
    { id: 3, name: 'Satin Evening Dress', price: 3299, rating: 4.9, stock: 12, image: img('1529139574466-a303027c1d8b') },
    { id: 4, name: 'A-Line Casual Dress', price: 1999, rating: 4.6, stock: 30, image: img('1485968579580-b6d095142e6e') },
    { id: 5, name: 'Wrap Style Dress', price: 2199, rating: 4.7, stock: 21, image: img('1509631179647-0177331693ae') }
  ],

  tops: [
    { id: 6, name: 'Oversized Cotton Shirt', price: 1299, rating: 4.7, stock: 28, image: img('1489987707025-afc232f7ea0f') },
    { id: 7, name: 'Puff Sleeve Blouse', price: 1499, rating: 4.8, stock: 20, image: img('1554568218-0f1715e72254') },
    { id: 8, name: 'Ribbed Knit Top', price: 999, rating: 4.6, stock: 35, image: img('1503342217505-b0a15ec3261c') },
    { id: 9, name: 'Printed Casual Top', price: 899, rating: 4.5, stock: 32, image: img('1521572163474-6864f9cf17ab') },
    { id: 10, name: 'High-Neck Blouse', price: 1599, rating: 4.7, stock: 16, image: img('1485462537746-965f33f7f6a7') }
  ],

  pants: [
    { id: 11, name: 'High-Waist Skinny Jeans', price: 1899, rating: 4.7, stock: 25, image: img('1541099649105-f69ad21f3246') },
    { id: 12, name: 'Wide-Leg Trousers', price: 1799, rating: 4.6, stock: 22, image: img('1594633312681-425c7b97ccd1') },
    { id: 13, name: 'Straight Fit Denim Jeans', price: 1999, rating: 4.8, stock: 26, image: img('1542272604-787c3835535d') },
    { id: 14, name: 'Cargo Pants', price: 1699, rating: 4.5, stock: 19, image: img('1515886657613-9f3515b0c78f') },
    { id: 15, name: 'Formal Office Pants', price: 1899, rating: 4.6, stock: 23, image: img('1594633313593-bab3825d0caf') }
  ],

  skirts: [
    { id: 16, name: 'Pleated Midi Skirt', price: 1499, rating: 4.7, stock: 20, image: img('1583496661160-fb5886a13d44') },
    { id: 17, name: 'Denim Mini Skirt', price: 1299, rating: 4.6, stock: 24, image: img('1554412933-514a83d2f3c8') },
    { id: 18, name: 'Satin Long Skirt', price: 1799, rating: 4.8, stock: 14, image: img('1583496661160-fb5886a13d44') },
    { id: 19, name: 'A-Line Skirt', price: 1399, rating: 4.5, stock: 22, image: img('1594223274512-ad4803739b7c') },
    { id: 20, name: 'Floral Printed Skirt', price: 1199, rating: 4.6, stock: 27, image: img('1554412933-514a83d2f3c8') }
  ],

  womenEthnic: [
    { id: 21, name: 'Embroidered Kurti', price: 1699, rating: 4.8, stock: 20, image: img('1610030469983-98e550d6193c') },
    { id: 22, name: 'Anarkali Dress', price: 2999, rating: 4.9, stock: 15, image: img('1583391733956-6c78276477e2') },
    { id: 23, name: 'Printed Kurta Set', price: 2199, rating: 4.7, stock: 18, image: img('1610030469983-98e550d6193c') },
    { id: 24, name: 'Palazzo Suit Set', price: 2499, rating: 4.6, stock: 16, image: img('1583391733956-6c78276477e2') },
    { id: 25, name: 'Festive Ethnic Gown', price: 3499, rating: 4.9, stock: 10, image: img('1610030469983-98e550d6193c') }
  ],

  // MEN
  shirts: [
    { id: 101, name: 'Slim Fit Formal Shirt', price: 1499, rating: 4.7, stock: 30, image: img('1603252109303-2751441dd157') },
    { id: 102, name: 'Linen Casual Shirt', price: 1299, rating: 4.8, stock: 24, image: img('1602810318383-e386cc2a3ccf') },
    { id: 103, name: 'Oxford Button Shirt', price: 1599, rating: 4.6, stock: 22, image: img('1598033129183-c4f50c736f10') },
    { id: 104, name: 'Checked Casual Shirt', price: 1199, rating: 4.5, stock: 28, image: img('1588359348347-9bc6cbbb689e') },
    { id: 105, name: 'Denim Shirt', price: 1799, rating: 4.7, stock: 20, image: img('1543076447-215ad9ba6923') }
  ],

  tshirts: [
    { id: 106, name: 'Basic Crew Neck Tee', price: 699, rating: 4.5, stock: 40, image: img('1521572163474-6864f9cf17ab') },
    { id: 107, name: 'Oversized Graphic Tee', price: 1199, rating: 4.8, stock: 35, image: img('1503341504253-dff4815485f1') },
    { id: 108, name: 'Polo T-Shirt', price: 999, rating: 4.6, stock: 30, image: img('1581655353564-df123a1eb820') },
    { id: 109, name: 'Henley T-Shirt', price: 1099, rating: 4.7, stock: 26, image: img('1562157873-818bc0726f68') },
    { id: 110, name: 'Striped Cotton Tee', price: 899, rating: 4.5, stock: 33, image: img('1521572163474-6864f9cf17ab') }
  ],

  trousers: [
    { id: 111, name: 'Slim Fit Jeans', price: 1899, rating: 4.7, stock: 27, image: img('1542272604-787c3835535d') },
    { id: 112, name: 'Straight Fit Denim', price: 1999, rating: 4.8, stock: 24, image: img('1541099649105-f69ad21f3246') },
    { id: 113, name: 'Chino Pants', price: 1699, rating: 4.6, stock: 31, image: img('1473966968600-fa801b869a1a') },
    { id: 114, name: 'Formal Trousers', price: 1899, rating: 4.7, stock: 22, image: img('1594938298603-c8148c4dae35') },
    { id: 115, name: 'Cargo Pants', price: 1799, rating: 4.5, stock: 20, image: img('1515886657613-9f3515b0c78f') }
  ],

  jackets: [
    { id: 116, name: 'Denim Jacket', price: 2499, rating: 4.8, stock: 18, image: img('1543076447-215ad9ba6923') },
    { id: 117, name: 'Bomber Jacket', price: 2999, rating: 4.7, stock: 14, image: img('1551028719-00167b16eac5') },
    { id: 118, name: 'Casual Blazer', price: 3499, rating: 4.6, stock: 12, image: img('1507680434567-5739c80be1ac') },
    { id: 119, name: 'Lightweight Windbreaker', price: 2199, rating: 4.5, stock: 20, image: img('1520975916090-3105956dac38') },
    { id: 120, name: 'Leather Look Jacket', price: 3999, rating: 4.9, stock: 10, image: img('1520975916090-3105956dac38') }
  ],

  menEthnic: [
    { id: 121, name: 'Classic Kurta', price: 1599, rating: 4.7, stock: 22, image: img('1598033129183-c4f50c736f10') },
    { id: 122, name: 'Kurta Pajama Set', price: 2499, rating: 4.8, stock: 18, image: img('1602810318383-e386cc2a3ccf') },
    { id: 123, name: 'Nehru Jacket Set', price: 2999, rating: 4.7, stock: 12, image: img('1507680434567-5739c80be1ac') },
    { id: 124, name: 'Festive Sherwani', price: 4999, rating: 4.9, stock: 8, image: img('1598033129183-c4f50c736f10') },
    { id: 125, name: 'Embroidered Kurta', price: 2199, rating: 4.8, stock: 16, image: img('1602810318383-e386cc2a3ccf') }
  ],

  // FOOTWEAR
  womenFootwear: [
    { id: 201, name: 'White Sneakers', price: 1899, rating: 4.8, stock: 30, image: img('1549298916-b41d501d3772') },
    { id: 202, name: 'Block Heels', price: 1799, rating: 4.6, stock: 18, image: img('1543163521-1bf539c55dd2') },
    { id: 203, name: 'Ballet Flats', price: 1299, rating: 4.5, stock: 24, image: img('1515347619252-60a4bf4fff4f') },
    { id: 204, name: 'Platform Sandals', price: 1599, rating: 4.7, stock: 20, image: img('1543163521-1bf539c55dd2') },
    { id: 205, name: 'Loafers', price: 1999, rating: 4.6, stock: 17, image: img('1515347619252-60a4bf4fff4f') }
  ],

  menFootwear: [
    { id: 206, name: 'Casual Sneakers', price: 1999, rating: 4.8, stock: 28, image: img('1549298916-b41d501d3772') },
    { id: 207, name: 'Formal Leather Shoes', price: 2999, rating: 4.7, stock: 16, image: img('1614252235316-8c857d38b5f4') },
    { id: 208, name: 'Loafers', price: 2199, rating: 4.6, stock: 19, image: img('1614252369475-531eba835eb1') },
    { id: 209, name: 'Running Shoes', price: 2499, rating: 4.8, stock: 25, image: img('1542291026-7eec264c27ff') },
    { id: 210, name: 'Chelsea Boots', price: 3499, rating: 4.7, stock: 12, image: img('1614252235316-8c857d38b5f4') }
  ],

  // ACCESSORIES
  handbags: [
    { id: 301, name: 'Tote Bag', price: 1599, rating: 4.8, stock: 24, image: img('1590874103328-eac38a683ce7') },
    { id: 302, name: 'Shoulder Bag', price: 1899, rating: 4.7, stock: 18, image: img('1584917865442-de89df76afd3') },
    { id: 303, name: 'Crossbody Bag', price: 2299, rating: 4.8, stock: 20, image: img('1584917865442-de89df76afd3') },
    { id: 304, name: 'Mini Handbag', price: 1399, rating: 4.6, stock: 22, image: img('1590874103328-eac38a683ce7') },
    { id: 305, name: 'Structured Office Bag', price: 2499, rating: 4.7, stock: 13, image: img('1584917865442-de89df76afd3') }
  ],

  jewelry: [
    { id: 306, name: 'Gold Plated Necklace', price: 899, rating: 4.7, stock: 35, image: img('1515562141207-7a88fb7ce338') },
    { id: 307, name: 'Pearl Earrings', price: 699, rating: 4.8, stock: 30, image: img('1535632066927-ab7c9ab60908') },
    { id: 308, name: 'Layered Chain Set', price: 999, rating: 4.6, stock: 26, image: img('1515562141207-7a88fb7ce338') },
    { id: 309, name: 'Bracelet Set', price: 799, rating: 4.5, stock: 28, image: img('1611591437281-460bfbe1220a') },
    { id: 310, name: 'Statement Ring', price: 599, rating: 4.6, stock: 32, image: img('1605100804763-247f67b3557e') }
  ],

  watches: [
    { id: 311, name: 'Rose Gold Watch', price: 2299, rating: 4.8, stock: 18, image: img('1523275335684-37898b6baf30') },
    { id: 312, name: 'Silver Analog Watch', price: 1999, rating: 4.7, stock: 20, image: img('1524592094714-0f0654e20314') },
    { id: 313, name: 'Minimalist Leather Watch', price: 2499, rating: 4.8, stock: 16, image: img('1524805444758-089113d48a6d') },
    { id: 314, name: 'Stainless Steel Watch', price: 2799, rating: 4.7, stock: 14, image: img('1523275335684-37898b6baf30') },
    { id: 315, name: 'Sports Watch', price: 1899, rating: 4.5, stock: 22, image: img('1508685096489-7aacd43bd3b1') }
  ],

  sunglasses: [
    { id: 316, name: 'Cat Eye Sunglasses', price: 999, rating: 4.6, stock: 25, image: img('1511499767150-a48a237f0083') },
    { id: 317, name: 'Oversized Shades', price: 1199, rating: 4.7, stock: 21, image: img('1577803645773-f96470509666') },
    { id: 318, name: 'Aviator Sunglasses', price: 1299, rating: 4.8, stock: 23, image: img('1511499767150-a48a237f0083') },
    { id: 319, name: 'Wayfarer Sunglasses', price: 1099, rating: 4.6, stock: 26, image: img('1577803645773-f96470509666') },
    { id: 320, name: 'Polarized Shades', price: 1499, rating: 4.7, stock: 18, image: img('1511499767150-a48a237f0083') }
  ],

  wallets: [
    { id: 321, name: 'Leather Wallet', price: 999, rating: 4.7, stock: 30, image: img('1627123424574-724758594e93') },
    { id: 322, name: 'Slim Card Holder', price: 699, rating: 4.5, stock: 34, image: img('1627123424574-724758594e93') },
    { id: 323, name: 'Bi-Fold Wallet', price: 899, rating: 4.6, stock: 27, image: img('1627123424574-724758594e93') },
    { id: 324, name: 'RFID Wallet', price: 1299, rating: 4.8, stock: 21, image: img('1627123424574-724758594e93') },
    { id: 325, name: 'Travel Wallet', price: 1499, rating: 4.7, stock: 17, image: img('1627123424574-724758594e93') }
  ],

  belts: [
    { id: 326, name: 'Formal Leather Belt', price: 899, rating: 4.7, stock: 32, image: img('1624222247344-550fb60583dc') },
    { id: 327, name: 'Casual Canvas Belt', price: 599, rating: 4.5, stock: 35, image: img('1624222247344-550fb60583dc') },
    { id: 328, name: 'Reversible Belt', price: 1099, rating: 4.6, stock: 24, image: img('1624222247344-550fb60583dc') },
    { id: 329, name: 'Brown Leather Belt', price: 999, rating: 4.7, stock: 25, image: img('1624222247344-550fb60583dc') },
    { id: 330, name: 'Automatic Buckle Belt', price: 1399, rating: 4.8, stock: 18, image: img('1624222247344-550fb60583dc') }
  ],

  // BEAUTY
  faceMakeup: [
    { id: 401, name: 'Liquid Foundation', price: 899, rating: 4.7, stock: 28, image: img('1596462502278-27bfdc403348') },
    { id: 402, name: 'BB Cream', price: 599, rating: 4.6, stock: 35, image: img('1596462502278-27bfdc403348') },
    { id: 403, name: 'Compact Powder', price: 699, rating: 4.5, stock: 30, image: img('1596462502278-27bfdc403348') },
    { id: 404, name: 'Concealer', price: 499, rating: 4.6, stock: 32, image: img('1596462502278-27bfdc403348') },
    { id: 405, name: 'Makeup Primer', price: 799, rating: 4.7, stock: 24, image: img('1596462502278-27bfdc403348') }
  ],

  lipMakeup: [
    { id: 406, name: 'Matte Lipstick', price: 699, rating: 4.8, stock: 40, image: img('1586495777744-4413f21062fa') },
    { id: 407, name: 'Liquid Lipstick', price: 799, rating: 4.7, stock: 34, image: img('1586495777744-4413f21062fa') },
    { id: 408, name: 'Lip Gloss', price: 499, rating: 4.5, stock: 37, image: img('1586495777744-4413f21062fa') },
    { id: 409, name: 'Lip Tint', price: 599, rating: 4.6, stock: 31, image: img('1586495777744-4413f21062fa') },
    { id: 410, name: 'Lip Liner', price: 399, rating: 4.5, stock: 39, image: img('1586495777744-4413f21062fa') }
  ],

  eyeMakeup: [
    { id: 411, name: 'Eyeshadow Palette', price: 1299, rating: 4.8, stock: 20, image: img('1512496015851-a90fb38ba796') },
    { id: 412, name: 'Waterproof Eyeliner', price: 499, rating: 4.7, stock: 35, image: img('1512496015851-a90fb38ba796') },
    { id: 413, name: 'Mascara', price: 699, rating: 4.6, stock: 32, image: img('1512496015851-a90fb38ba796') },
    { id: 414, name: 'Brow Pencil', price: 399, rating: 4.5, stock: 36, image: img('1512496015851-a90fb38ba796') },
    { id: 415, name: 'Kajal Pencil', price: 299, rating: 4.6, stock: 42, image: img('1512496015851-a90fb38ba796') }
  ],

  skincare: [
    { id: 416, name: 'Face Cleanser', price: 699, rating: 4.7, stock: 30, image: img('1556228578-8c89e6adf883') },
    { id: 417, name: 'Vitamin C Serum', price: 1299, rating: 4.9, stock: 24, image: img('1620916566398-39f1143ab7be') },
    { id: 418, name: 'Moisturizing Cream', price: 899, rating: 4.7, stock: 28, image: img('1556228578-8c89e6adf883') },
    { id: 419, name: 'Sunscreen SPF 50', price: 799, rating: 4.8, stock: 26, image: img('1620916566398-39f1143ab7be') },
    { id: 420, name: 'Face Toner', price: 599, rating: 4.6, stock: 33, image: img('1556228578-8c89e6adf883') }
  ],

  tools: [
    { id: 421, name: 'Makeup Brush Set', price: 999, rating: 4.7, stock: 25, image: img('1522335789203-aabd1fc54bc9') },
    { id: 422, name: 'Beauty Blender', price: 399, rating: 4.6, stock: 40, image: img('1522335789203-aabd1fc54bc9') },
    { id: 423, name: 'Eyelash Curler', price: 299, rating: 4.5, stock: 36, image: img('1522335789203-aabd1fc54bc9') },
    { id: 424, name: 'Makeup Organizer', price: 799, rating: 4.6, stock: 22, image: img('1522335789203-aabd1fc54bc9') },
    { id: 425, name: 'Compact Mirror', price: 249, rating: 4.5, stock: 45, image: img('1522335789203-aabd1fc54bc9') }
  ]
}