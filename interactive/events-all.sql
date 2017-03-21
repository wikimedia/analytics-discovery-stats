SELECT
        DATE(timestamp) AS day,
        event_action AS action,
        event_feature AS feature,
        SUM(event_sampling) AS events
    FROM Kartographer_16010805
    WHERE
        timestamp >= '{from_timestamp}'
        AND timestamp < '{to_timestamp}'
    GROUP BY event_action, event_feature

UNION ALL

SELECT
        DATE(timestamp) AS day,
        event_action AS action,
        event_feature AS feature,
        SUM(event_sampling) AS events
    FROM Kartographer_16132745_15423246
    WHERE
        timestamp >= '{from_timestamp}'
        AND timestamp < '{to_timestamp}'
    GROUP BY event_action, event_feature

UNION ALL

SELECT
        DATE(timestamp) AS day,
        event_action AS action,
        event_feature AS feature,
        SUM(event_sampling) AS events
    FROM Kartographer_16132745
    WHERE
        timestamp >= '{from_timestamp}'
        AND timestamp < '{to_timestamp}'
    GROUP BY event_action, event_feature
;
