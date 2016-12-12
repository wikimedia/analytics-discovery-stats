SELECT
        DATE(timestamp) AS day,
        event_action AS action,
        event_feature AS feature,
        SUM(event_sampling) AS events
    FROM Kartographer_16010805, metawiki.sites
    WHERE
        wiki = site_global_key
        AND (
            '{family}' = 'all'
            -- Non-special wikis
            OR site_group = '{family}'
            -- site_group for 'metawiki' is 'meta', etc
            OR ( '{family}' = 'special' AND INSTR(wiki, site_group) = 0 )
        )
        AND timestamp >= '{from_timestamp}'
        AND timestamp < '{to_timestamp}'
    GROUP BY event_action, event_feature
;
