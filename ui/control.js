const API_UPDATE="../api/update_control.php";
const API_GET="../api/get_control.php";
const API_SENSOR="../api/get_sensor.php";
const API_HIST="../api/get_sensor_history.php";
const API_STATUS="../api/get_device_status.php";

/* ===== DARK MODE ===== */
const toggleBtn=document.getElementById("themeToggle");

function applyTheme(t){
  document.body.classList.toggle("dark",t==="dark");
  toggleBtn.innerText=t==="dark"?"☀ Light":"🌙 Dark";
}
toggleBtn.onclick=()=>{
  const t=document.body.classList.contains("dark")?"light":"dark";
  localStorage.setItem("theme",t);
  applyTheme(t);
};
applyTheme(localStorage.getItem("theme")||"light");

/* ===== CONTROL ===== */
function postData(d){
  d.mode=1;
  return fetch(API_UPDATE,{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:new URLSearchParams(d)
  });
}
function setMode(m){postData({mode:m})}
function kipasOn(){postData({kipas:1})}
function kipasOff(){postData({kipas:0})}
function lampuOn(){postData({lampu:1})}
function lampuOff(){postData({lampu:0})}
function setServo(v){
  document.getElementById("servoValue").innerText=v+"°";
  postData({servo:v});
}

/* ===== SYNC ===== */
function syncControl(){
  fetch(API_GET).then(r=>r.json()).then(d=>{
    toggle("mode-manual",d.mode==1);
    toggle("mode-auto",d.mode==0);
    toggle("kipas-on",d.kipas==1);
    toggle("kipas-off",d.kipas==0);
    toggle("lampu-on",d.lampu==1);
    toggle("lampu-off",d.lampu==0);
    servoSlider.value=d.servo;
    servoValue.innerText=d.servo+"°";
  });
}

function syncSensor(){
  fetch(API_SENSOR).then(r=>r.json()).then(s=>{
    val("val-suhu",s.suhu+" °C");
    val("val-hum",s.kelembapan+" %");
    val("val-hujan",s.hujan?"HUJAN":"TIDAK");
    val("val-time",s.time);

    document.getElementById("bar-suhu").style.width=Math.min(s.suhu/50*100,100)+"%";
    document.getElementById("bar-hum").style.width=Math.min(s.kelembapan,100)+"%";
  });
}

function syncStatus(){
  fetch(API_STATUS).then(r=>r.json()).then(d=>{
    const el=deviceStatus;
    el.className="status "+(d.status==="ONLINE"?"online":"offline");
    el.innerText="ESP32 ● "+d.status;
  });
}

/* ===== CHART ===== */
let chart;
function initChart(){
  chart=new Chart(sensorChart,{
    type:"line",
    data:{labels:[],datasets:[
      {label:"Suhu",borderColor:"#dc2626",data:[]},
      {label:"Kelembapan",borderColor:"#0284c7",data:[]}
    ]}
  });
}
function updateChart(){
  fetch(API_HIST).then(r=>r.json()).then(d=>{
    chart.data.labels=d.map(x=>x.time);
    chart.data.datasets[0].data=d.map(x=>x.suhu);
    chart.data.datasets[1].data=d.map(x=>x.kelembapan);
    chart.update();
  });
}

/* ===== UTILS ===== */
function toggle(id,s){document.getElementById(id)?.classList.toggle("active",s)}
function val(id,v){document.getElementById(id).innerText=v}

/* ===== LOOP ===== */
setInterval(syncControl,2000);
setInterval(syncSensor,2000);
setInterval(syncStatus,3000);
setInterval(updateChart,5000);

window.onload=()=>{
  initChart();
  syncControl();
  syncSensor();
  syncStatus();
  updateChart();
};
