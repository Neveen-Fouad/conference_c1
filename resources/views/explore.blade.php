{{-- resources/views/explore.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Explore</title>
</head>
<body>

    <h1>Explore destinations</h1>

    <p>Countries loaded: {{ count($countries) }}</p>

    <div class="explore-form">
        <select id="country-select">
            <option value="">Select a country</option>
            @foreach($countries as $country)
                <option value="{{ $country['code2'] }}">{{ $country['name'] }}</option>
            @endforeach
        </select>

        <input type="text" id="city-input" placeholder="Enter a city, e.g. Cairo">

        <select id="interest-select">
            <option value="">Any interest</option>
            @foreach($interests as $interest)
                <option value="{{ $interest['slug'] }}">{{ $interest['name'] }}</option>
            @endforeach
        </select>

        <button id="search-btn">Search</button>
    </div>

    <div id="weather-card"></div>

    <h3>Attractions</h3>
    <div id="attractions-list"></div>

    <h3>Restaurants</h3>
    <div id="restaurants-list"></div>

    <script>
        document.getElementById('search-btn').addEventListener('click', async () => {
            const city = document.getElementById('city-input').value.trim();
            const countryCode = document.getElementById('country-select').value;
            const interest = document.getElementById('interest-select').value;

            if (!city) { alert('Please enter a city'); return; }

            const params = new URLSearchParams({ city, country_code: countryCode, interest });
            const res = await fetch(`/api/destination-data?${params}`);
            const data = await res.json();
            console.log(res.status);
            console.log(data);

            const weatherCard = document.getElementById('weather-card');
            if (data.weather) {
                weatherCard.innerHTML = `
                    <h3>${data.weather.location.name}, ${data.weather.location.country}</h3>
                    <img src="https:${data.weather.current.condition.icon}" alt="${data.weather.current.condition.text}">
                    <p><strong>${data.weather.current.temp_c}°C</strong></p>
                    <p>${data.weather.current.condition.text}</p>
                    <p>Humidity: ${data.weather.current.humidity}%</p>
                    <p>Wind: ${data.weather.current.wind_kph} km/h</p>
                `;
            } else {
                weatherCard.innerHTML = '<p>Weather unavailable for this city. Check the spelling and try again.</p>';
            }

            const attractionsList = document.getElementById('attractions-list');
            attractionsList.innerHTML = (data.attractions && data.attractions.length)
                ? data.attractions.map(a => `
                    <div class="card">
                        <h5>${a.name}</h5>
                        ${a.category ? `<p>${a.category}</p>` : ''}
                        ${a.rating ? `<p>Rating: ${a.rating}</p>` : ''}
                        ${a.address ? `<p>${a.address}</p>` : ''}
                    </div>
                `).join('')
                : '<p>No attractions found for this city.</p>';

            const restaurantsList = document.getElementById('restaurants-list');
            restaurantsList.innerHTML = (data.restaurants && data.restaurants.length)
                ? data.restaurants.map(r => `
                    <div class="card">
                        <h5>${r.name}</h5>
                        ${r.rating ? `<p>Rating: ${r.rating}</p>` : ''}
                        ${r.price_level ? `<p>${r.price_level}</p>` : ''}
                        ${r.address ? `<p>${r.address}</p>` : ''}
                    </div>
                `).join('')
                : '<p>No restaurants found for this city.</p>';
        });
    </script>

</body>
</html>