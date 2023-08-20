GATLING_BIN_DIR=$HOME/gatling/3.9.5/bin

WORKSPACE=$(pwd)

sh "$GATLING_BIN_DIR/gatling.sh" -rm local -s RinhaBackendSimulation \
    -rd "$WORKSPACE" \
    -rf "$WORKSPACE/user-files/results" \
    -sf "$WORKSPACE/user-files/simulations" \
    -rsf "$WORKSPACE/user-files/resources"

sleep 3

curl -v "http://localhost:9999/contagem-pessoas"
