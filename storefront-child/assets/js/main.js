(function() {
    let cityListWrapper;

    // Cities loading
    function loadCities(cityId = 0) {
        if (!cityListWrapper) return;

        cityListWrapper.classList.add('loading')
        cityListWrapper.classList.add('hide')

        const cityIdValueString = cityId ? cityId : ''

        const request = new XMLHttpRequest()
        request.open('GET', THEME_VARS.get_cities_api_endpoint_url + '?city_id=' + cityIdValueString, true)

        request.onreadystatechange = function() {
            if (request.readyState === XMLHttpRequest.DONE) {
                if (request.status === 200) {
                    const html = request.responseText

                    cityListWrapper.innerHTML = html
                    cityListWrapper.classList.remove('loading')
                    cityListWrapper.classList.remove('hide')

                } else {
                    console.log('Request error')
                }
            }
        }

        request.send()
    }

    // on load event initializing
    document.addEventListener('DOMContentLoaded', function () {
        cityListWrapper = document.getElementById('city_list')
        if (!cityListWrapper) return;

        const citySelect = document.getElementById('city_select')
        if (!citySelect) return;

        citySelect.addEventListener('change', function(e) {
            const value = e.target.value ? e.target.value : 0
            loadCities(value)
        })
    });
})()