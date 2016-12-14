SELECT
        DATE(NOW()) AS day,
        COUNT(*) AS num
    FROM page
    WHERE
        page_namespace = 486
        AND page_title LIKE '%.{type}'
;
