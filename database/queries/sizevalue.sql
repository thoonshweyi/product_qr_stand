SELECT * 
FROM public.product_specification_values AS psv
INNER JOIN products as prod
ON prod.id = psv.product_id
where specification_id = '10'
--and prod.category_id = 7
ORDER BY prod.id DESC LIMIT 100