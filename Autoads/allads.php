<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmes e Séries em Alta</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #222;
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-image 1s ease-in-out;
        }

        #movie-container {
            opacity: 0;
            width: 90%;
            max-width: 600px;
            position: relative;
            margin: auto;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-right: 0%;
        }

        .fade-in {
            animation: fadeIn 1s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .movie-details {
            color: #fff;
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 80%;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .movie-info {
            font-size: 25px;
            margin: -10px 0 5px;
            font-weight: bold;
            color: #FFD700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .movie-info-overview {
            font-size: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .movie-thumbnail {
            width: 100px;
            height: auto;
            margin-left: 20px;
        }

        .media-type-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .media-type {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            background-color: darkgray;
            margin-right: 20px; /* Adiciona um espaçamento fixo ao botão de tipo */
        }

        .media-type.filme { background-color: darkgreen; }
        .media-type.serie { background-color: darkred; }

        .release-year {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: darkgray;
        }

        .release-year.filme { background-color: Navy; }
        .release-year.serie { background-color: DarkMagenta; }

        @media (max-width: 768px) {
            #movie-container {
                margin-right: 5%;
            }
            .movie-info { font-size: 28px; }
            .movie-info-overview { font-size: 14px; }
        }

        @media (max-width: 480px) {
            #movie-container {
                margin-right: 2%;
            }
            .movie-info { font-size: 15px; }
            .movie-info-overview { font-size: 12px; }
            .movie-details { width: 100%; }
        }
    </style>
</head>
<body>
    <div id="movie-container">
        <div class="movie-details">
            <div>
                <div class="media-type-container">
                    <h3 id="media-type" class="media-type"></h3>
                    <h3 id="release-year" class="release-year"></h3>
                </div>
                <h1 id="movie-title" class="movie-info"></h1>
                <h3 id="movie-overview" class="movie-info-overview"></h3>
            </div>
            <img id="movie-thumbnail" class="movie-thumbnail" alt="Thumbnail" loading="lazy">
        </div>
    </div>

    <script>
        const apiKey = '1ab6ba39765d76b62c53391fa840744c';
        let currentIndex = 0;
        let movieIds = [];
        let seriesIds = [];
        let nextImage = null;

        const effects = ['fade-in'];

        async function fetchPopularIds() {
            try {
                const movieResponse = await fetch(`https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&sort_by=popularity.desc&language=pt-BR`);
                const movieData = await movieResponse.json();
                movieIds = movieData.results.map(movie => movie.id);

                const seriesResponse = await fetch(`https://api.themoviedb.org/3/discover/tv?api_key=${apiKey}&sort_by=popularity.desc&language=pt-BR`);
                const seriesData = await seriesResponse.json();
                seriesIds = seriesData.results.map(series => series.id);
            } catch (error) {
                console.error(error);
            }
        }

        function preloadNextImage() {
            if (movieIds.length === 0 || seriesIds.length === 0) return;

            const isMovie = currentIndex % 2 === 0;
            const currentId = isMovie ? movieIds[currentIndex] : seriesIds[currentIndex];
            const type = isMovie ? 'movie' : 'tv';

            fetch(`https://api.themoviedb.org/3/${type}/${currentId}?api_key=${apiKey}&language=pt-BR`)
                .then(response => response.json())
                .then(data => {
                    const backdropPath = `https://image.tmdb.org/t/p/original${data.backdrop_path}`;
                    nextImage = new Image();
                    nextImage.src = backdropPath;
                })
                .catch(error => console.error(error));
        }

        function getRandomEffect() {
            return effects[Math.floor(Math.random() * effects.length)];
        }

        function updateInfo() {
            if (movieIds.length === 0 || seriesIds.length === 0) return;

            const isMovie = currentIndex % 2 === 0;
            const currentId = isMovie ? movieIds[currentIndex] : seriesIds[currentIndex];
            const type = isMovie ? 'movie' : 'tv';

            fetch(`https://api.themoviedb.org/3/${type}/${currentId}?api_key=${apiKey}&language=pt-BR`)
                .then(response => response.json())
                .then(data => {
                    const movieContainer = document.getElementById('movie-container');
                    const randomEffect = getRandomEffect();

                    movieContainer.style.opacity = 0;

                    setTimeout(() => {
                        preloadNextImage();

                        movieContainer.classList.remove(...effects);
                        movieContainer.classList.add(randomEffect);

                        const movieTitle = document.getElementById('movie-title');
                        const movieOverview = document.getElementById('movie-overview');
                        const movieThumbnail = document.getElementById('movie-thumbnail');
                        const mediaType = document.getElementById('media-type');
                        const releaseYearElement = document.getElementById('release-year');

                        const backdropPath = `https://image.tmdb.org/t/p/original${data.backdrop_path}`;
                        document.body.style.backgroundImage = `url('${backdropPath}')`;

                        const posterPath = `https://image.tmdb.org/t/p/w500${data.poster_path}`;
                        movieThumbnail.src = posterPath;

                        const releaseDate = isMovie ? data.release_date : data.first_air_date;
                        const releaseYear = new Date(releaseDate).getFullYear();
                        const title = isMovie ? data.title : data.name;

                        movieTitle.innerText = title;
                        mediaType.innerText = isMovie ? 'Filme' : 'Série';
                        mediaType.className = `media-type ${isMovie ? 'filme' : 'serie'}`;
                        releaseYearElement.innerText = releaseYear;
                        releaseYearElement.className = `release-year ${isMovie ? 'filme' : 'serie'}`;

                        const overviewText = data.overview;
                        const overviewLines = overviewText.split('\n');
                        const shortenedOverview = overviewLines.slice(0, 3).join('\n');
                        movieOverview.innerText = shortenedOverview;

                        movieContainer.style.opacity = 1;
                    }, 1000);

                    currentIndex = (currentIndex + 1) % Math.min(movieIds.length, seriesIds.length);
                })
                .catch(error => console.error(error));
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchPopularIds().then(() => {
                preloadNextImage();
                updateInfo();
            });

            setInterval(preloadNextImage, 6000);
            setInterval(updateInfo, 7000);
        });
    </script>
</body>
</html>
