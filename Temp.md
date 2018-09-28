# 待解决  
## 分类查询商品mongo语句   
```
# debug
fecshop_test.command({"aggregate":"product_flat","pipeline":[{"$match":{"category":"57bea113f656f24e623bf56e","status":1}},{"$sort":{"score":-1}},{"$project":{"sku":1,"spu":1,"name":{"name_en":1},"image":1,"price":1,"special_price":1,"special_from":1,"special_to":1,"url_key":1,"score":1,"review_count":1,"favorite_count":1,"created_at":1,"qty":1,"final_price":1,"product_id":"$_id"}},{"$group":{"_id":"$spu","sku":{"$first":"$sku"},"spu":{"$first":"$spu"},"name":{"$first":"$name"},"image":{"$first":"$image"},"price":{"$first":"$price"},"special_price":{"$first":"$special_price"},"special_from":{"$first":"$special_from"},"special_to":{"$first":"$special_to"},"url_key":{"$first":"$url_key"},"score":{"$first":"$score"},"review_count":{"$first":"$review_count"},"favorite_count":{"$first":"$favorite_count"},"created_at":{"$first":"$created_at"},"qty":{"$first":"$qty"},"final_price":{"$first":"$final_price"},"product_id":{"$first":"$product_id"}}},{"$sort":{"score":-1}},{"$limit":6000}],"allowDiskUse":false,"cursor":{}})

####
获取产品 同一个spu的产品，有很多sku，但是只显示score最高的产品
# aggregate 聚合方法
db.getCollection('product_flat').aggregate([
		# 聚合表达式
		# $match 添加条件
        {
            "$match": {
                "category": "57bea113f656f24e623bf56e",
                "status": 1
            }
        },
        # $sort 对文档排序
        {
            "$sort": {
                "score": -1
            }
        },
        # $project 限制返回文档中的字段或者重命名其中的字段
        {
            "$project": {
            	# 1 为需要的字段
                "sku": 1,
                "spu": 1,
                "name": {
                    "name_en": 1
                },
                "image": 1,
                "price": 1,
                "special_price": 1,
                "special_from": 1,
                "special_to": 1,
                "url_key": 1,
                "score": 1,
                "review_count": 1,
                "favorite_count": 1,
                "created_at": 1,
                "qty": 1,
                "final_price": 1,
                # 重命名字段 把 $_id字段 重命名成 product_id
                "product_id": "$_id"
            }
        },
        # $group 分组 (对已$sort排序文档)
        {
            "$group": {
            	# 必须，以哪个作为主键id
                "_id": "$spu",
                "sku": {
                	# $first 获取排序第一个的文档数据
                    "$first": "$sku"
                },
                "spu": {
                    "$first": "$spu"
                },
                "name": {
                    "$first": "$name"
                },
                "image": {
                    "$first": "$image"
                },
                "price": {
                    "$first": "$price"
                },
                "special_price": {
                    "$first": "$special_price"
                },
                "special_from": {
                    "$first": "$special_from"
                },
                "special_to": {
                    "$first": "$special_to"
                },
                "url_key": {
                    "$first": "$url_key"
                },
                "score": {
                    "$first": "$score"
                },
                "review_count": {
                    "$first": "$review_count"
                },
                "favorite_count": {
                    "$first": "$favorite_count"
                },
                "created_at": {
                    "$first": "$created_at"
                },
                "qty": {
                    "$first": "$qty"
                },
                "final_price": {
                    "$first": "$final_price"
                },
                "product_id": {
                    "$first": "$product_id"
                }
            }
        },
        # $sort 这个是根据过滤的文档再排序，如再根据商品访问次数再排序
        {
            "$sort": {
                "review_count": -1
            }
        },
        # 限制输出的条数
        {
            "$limit": 6000
        }
    ])
```