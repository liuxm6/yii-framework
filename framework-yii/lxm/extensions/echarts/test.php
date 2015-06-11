<script>
	var myObject = {0:'0',1:'100',2:'200',3:'300',4:'400',5:'500',6:'600',7:'700',8:'800',9:'900',10:'1000',11:'1100',12:'1200',13:'1300',14:'1400',15:'1500',16:'1600',17:'1700',18:'1800',19:'1900',20:'2000',21:'2100',22:'2200',23:'2300',24:'2400',25:'2500',26:'2600',27:'2700',28:'2800',29:'2900',30:'3000',31:'3100',32:'3200',33:'3300',34:'3400',35:'3500',};
	</script>
<?php
$opt = <<<EOD
{
    title : {
        text: '玉米种子销量',
        subtext: '2014年度',
        x:'center'
    },
    tooltip : {
        trigger: 'item'
    },
    legend: {
        orient: 'vertical',
        x:'left',
        data:['玉米种子']
    },
    dataRange: {
        min: 0,
        max: 3500,
        text:['高','低'],           // 文本，默认为数值文本
        calculable : true,
        textStyle: {
            color: 'orange'
        }
    },
    series : [
        {
            name: '玉米种子',
            type: 'map',
            mapType: 'china',
            selectedMode : 'single',
            itemStyle:{
                normal:{label:{show:true}, color:'#ffd700'},// for legend
                emphasis:{label:{show:true}}
            },
            data:[
                {name: '北京',value: parseInt(myObject[1])},
                {name: '天津',value: parseInt(myObject[2])},
                {name: '上海',value: parseInt(myObject[3])},
                {name: '重庆',value: parseInt(myObject[4])},
                {name: '河北',value: parseInt(myObject[5])},
                {name: '河南',value: parseInt(myObject[6])},
                {name: '云南',value: parseInt(myObject[7])},
                {name: '辽宁',value: parseInt(myObject[8])},
                {name: '黑龙江',value: parseInt(myObject[9])},
                {name: '湖南',value: parseInt(myObject[10])},
                {name: '安徽',value: parseInt(myObject[11])},
                {name: '山东',value: parseInt(myObject[12])},
                {name: '新疆',value: parseInt(myObject[13])},
                {name: '江苏',value: parseInt(myObject[14])},
                {name: '浙江',value: parseInt(myObject[15])},
                {name: '江西',value: parseInt(myObject[16])},
                {name: '湖北',value: parseInt(myObject[17])},
                {name: '广西',value: parseInt(myObject[18])},
                {name: '甘肃',value: parseInt(myObject[19])},
                {name: '山西',value: parseInt(myObject[20])},
                {name: '内蒙古',value: parseInt(myObject[21])},
                {name: '陕西',value: parseInt(myObject[22])},
                {name: '吉林',value: parseInt(myObject[23])},
                {name: '福建',value: parseInt(myObject[24])},
                {name: '贵州',value: parseInt(myObject[25])},
                {name: '广东',value: parseInt(myObject[26])},
                {name: '青海',value: parseInt(myObject[27])},
                {name: '西藏',value: parseInt(myObject[28])},
                {name: '四川',value: parseInt(myObject[29])},
                {name: '宁夏',value: parseInt(myObject[30])},
                {name: '海南',value: parseInt(myObject[31])},
                {name: '台湾',value: parseInt(myObject[32])},
                {name: '香港',value: parseInt(myObject[33])},
                {name: '澳门',value: parseInt(myObject[34])}
            ]
        },
    ]
}

EOD;

$userjs=<<<EOD

	var ecConfig = require('echarts/config');
	myChart.on(ecConfig.EVENT.CLICK, function(param){
		alert(123);
	})
EOD;

$this->widget('mts.extensions.echarts.ChartMap', array('id'=>'main','option'=>$opt,'userjs'=>$userjs));