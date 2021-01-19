<template>
    <div>
        <div style="width: 100%; height: 500px; position:relative;">
            <div style="position: relative;">
                <div class="canvas-holder" style="">
                    <canvas id="canvas1" style="border: 1px solid #ccc;">
                        Ваш браузер не поддерживает элемент canvas.
                    </canvas>
                </div>
                <div id="canvas1_map_container">
                <canvas id="canvas1_map">
                    Ваш браузер не поддерживает элемент canvas.
                </canvas>
                </div>
            </div>

            <div class="mb-4 mt-4 row">
                <h3 class="col-md-3">Выбранные места: </h3> <h3 class="col-md-9">{{placesInLine}}</h3>
            </div>

            <div class="mt-4 mb-5 form-row">
                <div class="col-md-6 form-inline">
                    <label>Ваше имя</label>
                    <input type="text" class="form-control ml-2" v-model="name">
                    <button @click="buyTickets" class="btn btn-success float-left ml-2 mr-sm-2">Зарезервировать</button>
                </div>
                <div class="col-md-6">
                    <p class="text-dark text-right">
                        {{answer}}
                    </p>
                </div>
            </div>
        </div>
        <FlashMessage :position="'right bottom'"/>
    </div>
</template>
<script>
import schemeDesigner from '../../../public/js/scheme-designer-master/dist/scheme-designer'
export default {
    data: () => ({
            eventId: 0,
            places: {},
            portal:'leadbook.ru/test-task-api',
            protocol: 'https',
            choosedPlaces: [],
            placesInLine: '',
            name: '',
            answer: ''
        }),
    mounted() {
        this.protocol = this.$store.getters.protocol
        this.portal = this.$store.getters.portal
        this.eventId = this.$route.params.eventId
        this.getPlaces(this.eventId)
    },
    watch: {
        '$store.getters.protocol': function() {
            this.protocol = this.$store.getters.protocol
        },
        '$store.getters.portal': function() {
            this.portal = this.$store.getters.portal
        },
        protocol: function() {
            this.getPlaces(this.eventId)
        },
        portal: function() {
            this.getPlaces(this.eventId)
        }
    },
    methods: {
        getPlaces(id) {
            let url = this.protocol+'://'+this.portal+'/events/'+ id + '/places'
            axios.get(url)
                .then((data) => {
                    this.places = data.data.response;
                    console.log(this.places)
                    this.loadCanvas(this.places)
                })
                .catch( (error) => {
                        if(error.toString().includes('404')) {
                            error = 'Поставщик билетов не найден'
                        }
                        else if(error.toString().includes('Network Error')) {
                            error = 'Необходимо отключить CORS в браузере'
                        }
                        this.flashMessage.error({
                            title: 'Ошибка',
                            message: error,
                        });
                    });
        },
        buyTickets() {
            let url = this.protocol+'://'+this.portal+'/events/'+ this.eventId + '/reserve'
            let data = new FormData()
            data.append('name', this.name)
            data.append('places', this.choosedPlaces)

            if(this.name == '') {
                this.flashMessage.error({
                    title: 'Ошибка',
                    message: 'Вы не ввели своё имя',
                });
            }
            else if(this.choosedPlaces.length <=0) {
                this.flashMessage.error({
                    title: 'Ошибка',
                    message: 'Вы не выбрали места',
                });
            }
            else {
                axios.post(url, data)
                    .then((data) => {
                        let message = 'Ваша бронь ' + data.data.response.reservation_id
                        this.answer = message
                        this.flashMessage.success({
                            title: 'Успешно',
                            message: message,
                        });
                    })
                    .catch((error) => {
                        this.flashMessage.error({
                            title: 'Ошибка',
                            message: error,
                        });
                    });
            }
        },
        loadCanvas(places) {
            let backgroundLayer = new SchemeDesigner.Layer('background', {zIndex: 0, visible: false, active: false});
            let defaultLayer = new SchemeDesigner.Layer('default', {zIndex: 10});

            /**
             * Render place function
             * @param {SchemeObject} schemeObject
             * @param {Scheme} schemeDesigner
             * @param {View} view
             */
            let renderPlace = function (schemeObject, schemeDesigner, view) {
                var context = view.getContext();

                var backgroundColor = '#' + '00FFFF';

                context.beginPath();
                context.lineWidth = 4;
                context.strokeStyle = '#1682B4';

                var isHovered = schemeObject.is_available && !SchemeDesigner.Tools.touchSupported();

                context.fillStyle = backgroundColor;
                
                if (schemeObject.params.isSelected && isHovered) {
                    context.strokeStyle = backgroundColor;
                } else if (isHovered) {
                    context.fillStyle = 'white';
                    context.strokeStyle = backgroundColor;
                } else if (schemeObject.params.isSelected) {
                    context.strokeStyle = backgroundColor;
                }

                var relativeX = schemeObject.x;
                var relativeY = schemeObject.y;

                var width = schemeObject.width;
                var height = schemeObject.height;
                if (!isHovered && !schemeObject.params.isSelected) {
                    var borderOffset = 4;
                    relativeX = relativeX + borderOffset;
                    relativeY = relativeY + borderOffset;
                    width = width - (borderOffset * 2);
                    height = height - (borderOffset * 2);
                }
                
                var halfWidth = width / 2;
                var halfHeight = height / 2;

                var circleCenterX = relativeX + halfWidth;
                var circleCenterY = relativeY + halfHeight;

                if (schemeObject.rotation) {
                    context.save();
                    context.translate( relativeX + halfWidth, relativeY + halfHeight);
                    context.rotate(schemeObject.rotation * Math.PI / 180);
                    context.rect(-halfWidth, -halfHeight, width, height);
                } else {
                    context.arc(circleCenterX, circleCenterY, halfWidth, 0, Math.PI * 2);
                }


                context.fill();
                context.stroke();

                context.font = (Math.floor((schemeObject.width + schemeObject.height) / 4)) + 'px Arial';

                if (schemeObject.params.isSelected && isHovered) {
                    context.fillStyle = 'white';
                } else if (isHovered) {
                    context.fillStyle = backgroundColor;
                } else if (schemeObject.params.isSelected) {
                    context.fillStyle = 'black';
                }

                if (schemeObject.params.isSelected || isHovered) {
                    context.textAlign = 'center';
                    context.textBaseline = 'middle';
                    if (schemeObject.rotation) {
                        context.fillText(schemeObject.params.seat,
                                -(schemeObject.width / 2) + (schemeObject.width / 2),
                                -(schemeObject.height / 2)  + (schemeObject.height / 2)
                        );
                    } else {
                        context.fillText(schemeObject.params.seat, relativeX + (schemeObject.width / 2), relativeY + (schemeObject.height / 2));
                    }
                }

                if (schemeObject.rotation) {
                    context.restore();
                }
            };

            /**
             * Render label function
             * @param {SchemeObject} schemeObject
             * @param {Scheme} schemeDesigner
             * @param {View} view
             */
            let renderLabel = function(schemeObject, schemeDesigner, view) {
                var fontSize = (schemeObject.params.fontSize >> 0) * (96 / 72) * 3;

                var context = view.getContext();

                context.fillStyle = '#' + this.params.fontColor;
                context.font = fontSize + 'px Arial';
                context.textAlign = 'center';
                context.textBaseline = 'middle';
                context.fillText(schemeObject.params.sectorName, schemeObject.x, schemeObject.y);
            };


            let clickOnPlace = function (schemeObject, schemeDesigner, view, e)
            {
                schemeObject.is_available = !schemeObject.is_available;
                let index = this.choosedPlaces.indexOf(schemeObject.id)
                if (index != -1) {
                    this.choosedPlaces.splice(index, 1);
                }
                else {
                    this.choosedPlaces.push(parseInt(schemeObject.id));
                }
                console.log(schemeObject.id)
                this.placesInLine = this.choosedPlaces.join(', ')
            }.bind(this);

            /**
             * Creating places
             */
            for (let i = 0; i < places.length; i++)
            {
                var objectData = places[i];
                var leftOffset = objectData.y >> 0;
                var topOffset = objectData.x >> 0;
                var width = objectData.width >> 0;
                var height = objectData.height >> 0;
                var rotation = 0 >> 0;

                var schemeObject = new SchemeDesigner.SchemeObject({
                    /**
                     * Required params
                     */
                    x: 10 + leftOffset,
                    y: 10 + topOffset,
                    width: width,
                    height: height,
                    active: objectData.is_available,
                    renderFunction: renderPlace,
                    cursorStyle: 'pointer',

                    /**
                     * Custom params (any names and count)
                     */
                    rotation: rotation,
                    id: objectData.id,
                    //price: objectData.Price,
                    seat: objectData.id,
                    row: objectData.Row,
                    //sectorName: objectData.Name_sec,
                    fontSize: 0,
                    backgroundColor: objectData.is_available ? '#00FFFF' : '#FF4500',
                    fontColor: objectData.is_available ? '#4682B4' : '#000000',

                    isSelected: !objectData.is_available,
                    clickFunction: clickOnPlace,
                    clearFunction: function (schemeObject, schemeDesigner, view) {
                        var context = view.getContext();

                        var borderWidth = 5;
                        context.clearRect(schemeObject.x - borderWidth,
                                schemeObject.y - borderWidth,
                                this.width + (borderWidth * 2),
                                this.height + (borderWidth * 2)
                        );
                    }
                });

                defaultLayer.addObject(schemeObject);
            }

            /**
             * add background object
             */
            backgroundLayer.addObject(new SchemeDesigner.SchemeObject({
                x: 0.5,
                y: 0.5,
                width: 8600,
                height: 7000,
                cursorStyle: 'default',
                renderFunction: function (schemeObject, schemeDesigner, view) {
                    var context = view.getContext();
                    context.beginPath();
                    context.lineWidth = 4;
                    context.strokeStyle = 'rgba(12, 200, 15, 0.2)';

                    context.fillStyle = 'rgba(12, 200, 15, 0.2)';


                    var width = schemeObject.width;
                    var height = schemeObject.height;

                    context.rect(schemeObject.x, schemeObject.y, width, height);


                    context.fill();
                    context.stroke();
                }
            }));

            let canvas = document.getElementById('canvas1');
            let mapCanvas = document.getElementById('canvas1_map');

            let schemeDesigner = new SchemeDesigner.Scheme(canvas, {
                options: {
                    cacheSchemeRatio: 2
                },
                scroll: {
                    maxHiddenPart: 0.5
                },
                zoom: {
                    padding: 0.1,
                    maxScale: 8,
                    zoomCoefficient: 1.04
                },
                storage: {
                    treeDepth: 6
                },
                map: {
                    mapCanvas: mapCanvas
                }
            });

            /**
             * Adding layers with objects
             */
            schemeDesigner.addLayer(defaultLayer);
            schemeDesigner.addLayer(backgroundLayer);

            /**
             * Show scheme
             */
            schemeDesigner.render();



            canvas.addEventListener('schemeDesigner.beforeRenderAll', function (e) {
              //  console.time('renderAll');
            }, false);

            canvas.addEventListener('schemeDesigner.afterRenderAll', function (e) {
            //    console.timeEnd('renderAll');
            }, false);

            canvas.addEventListener('schemeDesigner.clickOnObject', function (e) {
                console.log('clickOnObject', e.detail);
            }, false);

            canvas.addEventListener('schemeDesigner.mouseOverObject', function (e) {
                //console.log('mouseOverObject', e.detail);
            }, false);

            canvas.addEventListener('schemeDesigner.mouseLeaveObject', function (e) {
            //  console.log('mouseLeaveObject', e.detail);
            }, false);

            canvas.addEventListener('schemeDesigner.scroll', function (e) {
            //  console.log('scroll', e.detail);
            }, false);
        }
    }
}
</script>

<style scoped>
    .canvas-holder {
        width: 100%;
        height: 500px;
        position:relative;
    }
    #canvas1_map_container {
        width: 150px;
        height: 150px;
        position: absolute;
        top: 2px;
        right: 0;
        background: white;
        border: 1px solid #ddd;
    }
    @media (max-width: 767px) {
        #canvas1_map_container {
            display: none;
        }
    }
</style>