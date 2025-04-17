<?php

// Replace 'YOUR_API_KEY' with your actual TMDB API key
$apiKey = '6b8e3eaa1a03ebb45642e9531d8a76d2';

// Initialize an empty array to store the results
$allMovies = [];

// Iterate over pages 1 to 5
for ($page = 1; $page <= 5; $page++) {
    // Construct the URL for the current page
    $url = "https://api.themoviedb.org/3/movie/popular?api_key={$apiKey}&language=en-US&page={$page}";

    // Fetch data from the TMDB API
    $response = file_get_contents($url);

    // Check if the request was successful
    if ($response !== false) {
        // Decode the JSON response
        $data = json_decode($response, true);

        // Extract movie results from the response
        $movies = $data['results'];

        // Merge movie results into the array
        $allMovies = array_merge($allMovies, $movies);
    } else {
        // Handle error if request fails
        echo "Failed to retrieve data for page {$page}.\n";
    }
}

// Encode all movies as JSON
$allMoviesJson = json_encode($allMovies);

// Output the JSON response
header('Content-Type: application/json');
echo $allMoviesJson;
