from flask import Flask, jsonify, request
from flask_cors import CORS
import requests

app = Flask(__name__)
CORS(app)    # Ensure CORS is enabled

# Home route to test if the API is running
@app.route('/')
def home():
    return jsonify({"message": "Weather API is live!"})

# Your OpenWeatherMap API key
api_key = '6dccae404f96466c4fde6c0a23d0c32c'

@app.route('/weather', methods=['GET'])
def get_weather():
    # Get the location from the query parameter (e.g., /weather?location=London)
    user_location = request.args.get('location')
    
    if not user_location:
        return jsonify({"error": "Please provide a location"}), 400

    # Fetch weather data from OpenWeatherMap API
    weather_data = requests.get(
        f"https://api.openweathermap.org/data/2.5/weather?q={user_location}&units=imperial&APPID={api_key}"
    )

    # Check if the city was found
    if weather_data.json()['cod'] != 200:
        return jsonify({"error": "City not found, please try again"}), 404

    # Extract relevant weather information
    weather = weather_data.json()['weather'][0]['main']
    temp_in_fahrenheit = weather_data.json()['main']['temp']
    temp = round((5 / 9) * (temp_in_fahrenheit - 32))  # Convert to Celsius
    pressure = weather_data.json()['main']['pressure']
    wind_speed = weather_data.json()['wind']['speed']
    humidity = weather_data.json()['main']['humidity']

    # Return the weather data as JSON
    return jsonify({
        "location": user_location,
        "weather": weather,
        "temperature": f"{temp}°C",
        "pressure": pressure,
        "wind_speed": f"{wind_speed} m/s",
        "humidity": f"{humidity}%"
    })

if __name__ == '__main__':
    app.run(debug=True)
