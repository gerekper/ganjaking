<template>
    <div class="steps">
        <div v-if="points" v-for="(item, i) in points[0]" class="step step-1">
            <div class="line active">
                <div class="point" :class="{pulse : i === points[0].length - 1}"></div>
                <div class="text">{{item}}</div>
            </div>
        </div>

        <div v-for="item in points[1]" class="step step-1">
            <div class="line">
                <div class="point"></div>
                <div class="text">{{item}}</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "steps",
        props: [
            'points'
        ],
        data() {
            return {}
        },
        created() {
            //console.log(this.points);

        }
    }
</script>

<style scoped lang="scss">
    .steps {
        padding: 40px 20px;
        height: 100px;
        display: flex;
        justify-content: space-between;

        .point {
            background: #C4CDD5;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            position: absolute;
            left: 50%;
            margin-left: -10px;
            top: -10px;
        }

        .text {
            text-align: center;
            margin-top: 15px;
        }

        .line {
            position: relative;
            border-top: 1px solid #C4CDD5;

            &.active {
                border-color: #1daeea;

                .point {
                    background: #1daeea;

                }
            }
        }

        .step {
            flex: 2;

            &:first-child {
                flex: 1;

                .point {
                    left: 0;
                    margin-left: 0;
                }

                .text {
                    text-align: left;
                }

            }

            &:last-child {
                flex: 1;

                .point {
                    right: 0;
                    margin-left: 0;
                    left: auto;
                }

                .text {
                    text-align: right;
                }

            }
        }
    }

    .pulse {
        cursor: pointer;
        box-shadow: 0 0 0 rgba(29, 174, 234, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(29, 174, 234, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(29, 174, 234, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(29, 174, 234, 0);
        }
    }
</style>