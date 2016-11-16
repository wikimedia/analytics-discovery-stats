SELECT
        DATE(timestamp) AS day,
        event_action AS action,
        event_feature AS feature,
        SUM(event_sampling) AS events
    FROM Kartographer_16010805
    WHERE
        ('{wiki}' = 'all' OR wiki = '{wiki}')
        AND timestamp >= '{from_timestamp}'
        AND timestamp < '{to_timestamp}'
        AND event_action IN (
            'view',
            'open',
            'close',
            'hashopen'
        )
    GROUP BY event_action, event_feature
;
