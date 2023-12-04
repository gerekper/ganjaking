import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { select } from '@wordpress/data';

const useFetch = (path, dependencies = []) => {
    const [isLoading, setIsLoading] = useState(false);
    const [data, setData] = useState([]);
    const [error, setError] = useState(null)

    useEffect(() => {
        setIsLoading(true);
        apiFetch({ path })
            .then((response) => {
                const data = response && response?.length > 0 ? response : [];
                setData(data);
            })
            .catch((error) => {
                console.error("BetterDocs useFetch ERROR: ", error);
                setError(error);
            })
            .finally(() => setIsLoading(false));
    }, [path])

    return { data, isLoading, error };
}

export default useFetch
