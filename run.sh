export result=${PWD##*/}
#res=${PWD}
#docker kill $(docker ps -q)
docker kill $result
docker rm $result
docker-compose up -d
