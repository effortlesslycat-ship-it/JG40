// Cities.js
// ~~~~~~~~~
// JavaScript functions for Gazetteer & Communities DB.
// Warren Blatt, May 2006, Jan 2007, Jan 2008.


var city_latlong = "";

var latd = document.getElementById('c1');
var latm = document.getElementById('c2');
var lath = document.getElementById('LATHEM');
var lond = document.getElementById('d1');
var lonm = document.getElementById('d2');
var lonh = document.getElementById('LONGHEM');

var coun   = document.getElementById('coun');    // Country select pulldown
var cityCh = document.getElementById('cities');  // City    select pulldown
var ccity  = document.getElementById('ccity');   // hidden FORM variable


// OnChange event handler for Country SELECT box:
function setCities(country)
{
  // debug:
  // alert ("Setting cities for: '" + country + "'");

  // Clear previous settings of the Cities pulldown:
  cityCh.options.length = 0;

  // Create initial option:
  cityCh.options[0] = new Option ("Select City", "        ");

  // Populate options in the cities pulldown:
  var index = 1;
  var label = "";
  for (var i = 0; i < cit.length; i++)
    {
      if ( country == cit[i].co )
        {
          label = cit[i].ci;
          if (cit[i].lv == "2")
            { label = " - - " + label; }

          var y = document.createElement('span');
          y.innerHTML = label;

	  cityCh.options[index++] = new Option (y.innerHTML, cit[i].ll);

	  // Region-level labels shouldn't be selectable.
          if (cit[i].lv == "1")
            { cityCh.options[index-1].disabled = true; }
        }
    }

  // Provide a default option if this country has no cities:
  if ( index == 1 )
    { cityCh.options[0] = new Option ("No cities", "        ", true, false); }

  // Set the sensitivity of the Cities pulldown:
  cityCh.disabled = ( index == 1 );

  // Clear the four numeric lat/long boxes:
  selCity(null);
}


// OnChange event handler for City SELECT box:
function selCity(SelectObj)
{
  // city_latlong is a document-level variable.

  var city_name = "";
  if ( ! SelectObj )
    {
      city_latlong = "";

      // Set values in the four lat/long boxes:
      latd.value = "";
      latm.value = "";
      lond.value = "";
      lonm.value = "";
    }
  else
    {
      city_latlong = SelectObj.value;

      if ( SelectObj.length > 1 )
        {
          city_name = SelectObj.options[SelectObj.selectedIndex].text;

          // Set values in the lat/long boxes:
          latd.value = city_latlong.substring(0,2);
          latm.value = city_latlong.substring(2,4);
          lath.value = city_latlong.substring(4,5);
          lond.value = city_latlong.substring(6,8);
          lonm.value = city_latlong.substring(8,10);
          lonh.value = city_latlong.substring(10,11);
        }
      else  // if no selection, blank out values:
        {
          latd.value = "";
          latm.value = "";
          lond.value = "";
          lonm.value = "";
        }
    }

  // Set value of the hidden "ccity" formvar:
  city_name = city_name.replace (/(- - )/g, "");  // strip leading "- - "
  // ccity.value = encodeURIComponent(city_name);
  // var y = document.createElement('span');
  // y.innerHTML = city_name;
  // ccity.value = y.innerHTML;
  ccity.value = city_name;


  return true;
}


// Set the sensitivites when one of the radio buttons for
//   "Show the distance and direction from:" is selected.
function SensRadio(label)
{
  if (label == "capital")
    {
      latd.disabled = 1;  latm.disabled = 1;  lath.disabled = 1;
      lond.disabled = 1;  lonm.disabled = 1;  lonh.disabled = 1;
      coun.disabled = 1;
      cityCh.disabled = 1;
    }
  else if (label == "latlon")
    {
      latd.disabled = 0;  latm.disabled = 0;  lath.disabled = 0;
      lond.disabled = 0;  lonm.disabled = 0;  lonh.disabled = 0;
      coun.disabled = 1;
      cityCh.disabled = 1;
    }
  else if (label == "city")
    {
      latd.disabled = 0;  latm.disabled = 0;  lath.disabled = 0;
      lond.disabled = 0;  lonm.disabled = 0;  lonh.disabled = 0;
      coun.disabled = 0;
      // Disable Cities pulldown iff only 1 element.
      cityCh.disabled = (cityCh.length == 1);
    }
  else
    {
      alert ("SensRadio(): Unknown Label: '" + label + "'");
    }
}


// Window.OnLoad callback for LocTown.asp & Search.asp,
//   to initialize everything:
function initShtetlSeeker()
{
  initCities();
  var latlon = get_cookie(window.location.pathname.toLowerCase());
  var country = getOptionValue('coun');
  setCities(country);
  setOptionValue('cities', latlon);
  selCity(cityCh);
  SensRadio (getCheckedValue(document.forms['f'].elements['cl']));
}


var cit = null;     // array of reference cities

// Initialize the array of reference cities.
function initCities()
{

if (cit != null)    // only need to initialize array once
  { return; }

cit = new Array();
var n = 0;

cit[n++] = {co:"Alg", ll:"3645N 0303E", lv:"2", ci:"Algiers"};
cit[n++] = {co:"Alg", ll:"3621N 0636E", lv:"2", ci:"Constantine"};
cit[n++] = {co:"Alg", ll:"3541N 0038W", lv:"2", ci:"Oran"};

cit[n++] = {co:"Aus", ll:"4704N 1527E", lv:"2", ci:"Graz (Gratz)"};
cit[n++] = {co:"Aus", ll:"4735N 1628E", lv:"2", ci:"Lackenbach"};
cit[n++] = {co:"Aus", ll:"4818N 1418E", lv:"2", ci:"Linz"};
cit[n++] = {co:"Aus", ll:"4812N 1622E", lv:"2", ci:"Wien (Vienna)"};

cit[n++] = {co:"Bel", ll:"5309N 2914E", lv:"2", ci:"Babruysk (Bobruisk)"};
cit[n++] = {co:"Bel", ll:"5206N 2342E", lv:"2", ci:"Brest (Brest-Litovsk)"};
cit[n++] = {co:"Bel", ll:"5534N 2813E", lv:"2", ci:"Dzisna (Disna)"};
cit[n++] = {co:"Bel", ll:"5227N 3059E", lv:"2", ci:"Homyel (Gomel)"};
cit[n++] = {co:"Bel", ll:"5341N 2350E", lv:"2", ci:"Hrodna (Grodno)"};
cit[n++] = {co:"Bel", ll:"5213N 2422E", lv:"2", ci:"Kobryn"};
cit[n++] = {co:"Bel", ll:"5353N 2518E", lv:"2", ci:"Lida"};
cit[n++] = {co:"Bel", ll:"5355N 3020E", lv:"2", ci:"Mahilyow (Mogilev)"};
cit[n++] = {co:"Bel", ll:"5203N 2916E", lv:"2", ci:"Mazyr (Mozyr)"};
cit[n++] = {co:"Bel", ll:"5354N 2734E", lv:"2", ci:"Minsk"};
cit[n++] = {co:"Bel", ll:"5336N 2550E", lv:"2", ci:"Navahrudak (Nowogr&#243;dek)"};
cit[n++] = {co:"Bel", ll:"5207N 2604E", lv:"2", ci:"Pinsk"};
cit[n++] = {co:"Bel", ll:"5529N 2848E", lv:"2", ci:"Polatsk (Polotsk)"};
cit[n++] = {co:"Bel", ll:"5306N 2519E", lv:"2", ci:"Slonim"};
cit[n++] = {co:"Bel", ll:"5302N 2734E", lv:"2", ci:"Slutsk"};
cit[n++] = {co:"Bel", ll:"5204N 2744E", lv:"2", ci:"Turov"};
cit[n++] = {co:"Bel", ll:"5512N 3012E", lv:"2", ci:"Vitsyebsk (Vitebsk)"};

cit[n++] = {co:"Bul", ll:"4230N 2728E", lv:"2", ci:"Burgas"};
cit[n++] = {co:"Bul", ll:"4212N 2420E", lv:"2", ci:"Pazardzhik"};
cit[n++] = {co:"Bul", ll:"4209N 2445E", lv:"2", ci:"Plovdiv"};
cit[n++] = {co:"Bul", ll:"4350N 2557E", lv:"2", ci:"Ruse"};
cit[n++] = {co:"Bul", ll:"4316N 2655E", lv:"2", ci:"Shumen"};
cit[n++] = {co:"Bul", ll:"4241N 2319E", lv:"2", ci:"Sofiya"};
cit[n++] = {co:"Bul", ll:"4313N 2755E", lv:"2", ci:"Varna"};

cit[n++] = {co:"Cro", ll:"4533N 1842E", lv:"2", ci:"Osijek (Eszek)"};
cit[n++] = {co:"Cro", ll:"4521N 1425E", lv:"2", ci:"Rijeka (Fiume)"};
cit[n++] = {co:"Cro", ll:"4548N 1600E", lv:"2", ci:"Zagreb"};

cit[n++] = {co:"Cz",  ll:"4912N 1638E", lv:"2", ci:"Brno (Br&uuml;nn)"};
cit[n++] = {co:"Cz",  ll:"5013N 1254E", lv:"2", ci:"Karlovy Vary (Carlsbad)"};
cit[n++] = {co:"Cz",  ll:"5002N 1512E", lv:"2", ci:"Kol&iacute;n"};
cit[n++] = {co:"Cz",  ll:"4848N 1638E", lv:"2", ci:"Mikulov (Nikolsburg)"};
cit[n++] = {co:"Cz",  ll:"4950N 1817E", lv:"2", ci:"Moravsk&#225; Ostrava"};
cit[n++] = {co:"Cz",  ll:"4935N 1715E", lv:"2", ci:"Olomouc (Olm&uuml;tz)"};
cit[n++] = {co:"Cz",  ll:"4945N 1322E", lv:"2", ci:"Plze&#328; (Pilsen)"};
cit[n++] = {co:"Cz",  ll:"5005N 1428E", lv:"2", ci:"Praha (Prague)"};
cit[n++] = {co:"Cz",  ll:"4928N 1707E", lv:"2", ci:"Prost&#283;jov (Prossnitz) "};
cit[n++] = {co:"Cz",  ll:"5038N 1350E", lv:"2", ci:"Teplice (Teplitz)"};

cit[n++] = {co:"Fra", ll:"4450N 0034W", lv:"2", ci:"Bordeaux"};
cit[n++] = {co:"Fra", ll:"4545N 0451E", lv:"2", ci:"Lyon"};
cit[n++] = {co:"Fra", ll:"4318N 0524E", lv:"2", ci:"Marseille"};
cit[n++] = {co:"Fra", ll:"4908N 0610E", lv:"2", ci:"Metz"};
cit[n++] = {co:"Fra", ll:"4841N 0612E", lv:"2", ci:"Nancy"};
cit[n++] = {co:"Fra", ll:"4713N 0133W", lv:"2", ci:"Nantes"};
cit[n++] = {co:"Fra", ll:"4852N 0220E", lv:"2", ci:"Paris"};
cit[n++] = {co:"Fra", ll:"4835N 0745E", lv:"2", ci:"Strasbourg"};

cit[n++] = {co:"Ger", ll:"5231N 1324E", lv:"2", ci:"Berlin"};
cit[n++] = {co:"Ger", ll:"5044N 0706E", lv:"2", ci:"Bonn"};
cit[n++] = {co:"Ger", ll:"5131N 0727E", lv:"2", ci:"Dortmund"};
cit[n++] = {co:"Ger", ll:"5007N 0841E", lv:"2", ci:"Frankfurt am Main"};
cit[n++] = {co:"Ger", ll:"4928N 1058E", lv:"2", ci:"F&uuml;rth"};
cit[n++] = {co:"Ger", ll:"5333N 1000E", lv:"2", ci:"Hamburg"};
cit[n++] = {co:"Ger", ll:"5222N 0943E", lv:"2", ci:"Hannover"};
cit[n++] = {co:"Ger", ll:"5119N 0930E", lv:"2", ci:"Kassel"};
cit[n++] = {co:"Ger", ll:"5021N 0736E", lv:"2", ci:"Koblenz"};
cit[n++] = {co:"Ger", ll:"5056N 0657E", lv:"2", ci:"Köln (Cologne)"};
cit[n++] = {co:"Ger", ll:"5118N 1220E", lv:"2", ci:"Leipzig"};
cit[n++] = {co:"Ger", ll:"4929N 0828E", lv:"2", ci:"Mannheim"};
cit[n++] = {co:"Ger", ll:"4809N 1135E", lv:"2", ci:"M&uuml;nchen (Munich)"};
cit[n++] = {co:"Ger", ll:"4927N 1104E", lv:"2", ci:"N&uuml;rnberg (Nuremberg)"};
cit[n++] = {co:"Ger", ll:"4846N 0911E", lv:"2", ci:"Stuttgart"};
cit[n++] = {co:"Ger", ll:"4948N 0957E", lv:"2", ci:"W&uuml;rzburg"};

cit[n++] = {co:"Gre", ll:"3759N 2344E", lv:"2", ci:"Ath&iacute;nai (Athens)"};
cit[n++] = {co:"Gre", ll:"3940N 2050E", lv:"2", ci:"Io&#225;nnina (Yanya)"};
cit[n++] = {co:"Gre", ll:"4031N 2116E", lv:"2", ci:"Kastori&#225; (Kesriye)"};
cit[n++] = {co:"Gre", ll:"4056N 2424E", lv:"2", ci:"Kav&#225;la (Kavalla)"};
cit[n++] = {co:"Gre", ll:"3937N 1955E", lv:"2", ci:"K&eacute;rkyra (Corfu)"};
cit[n++] = {co:"Gre", ll:"3938N 2225E", lv:"2", ci:"L&#225;risa (Larissa)"};
cit[n++] = {co:"Gre", ll:"4038N 2256E", lv:"2", ci:"Thessalon&iacute;ki (Salonica)"};

cit[n++] = {co:"Hun", ll:"4641N 2106E", lv:"2", ci:"B&eacute;k&eacute;scsaba"};
cit[n++] = {co:"Hun", ll:"4730N 1905E", lv:"2", ci:"Budapest"};
cit[n++] = {co:"Hun", ll:"4732N 2138E", lv:"2", ci:"Debrecen"};
cit[n++] = {co:"Hun", ll:"4741N 1738E", lv:"2", ci:"Gy&#337;r"};
cit[n++] = {co:"Hun", ll:"4622N 1748E", lv:"2", ci:"Kaposv&#225;r"};
cit[n++] = {co:"Hun", ll:"4813N 2205E", lv:"2", ci:"Kisv&#225;rda"};
cit[n++] = {co:"Hun", ll:"4806N 2047E", lv:"2", ci:"Miskolc"};
cit[n++] = {co:"Hun", ll:"4627N 1659E", lv:"2", ci:"Nagykanizsa"};
cit[n++] = {co:"Hun", ll:"4757N 2143E", lv:"2", ci:"Ny&iacute;regyh&#225;za"};
cit[n++] = {co:"Hun", ll:"4824N 2140E", lv:"2", ci:"S&#225;toralja&#369;jhely"};
cit[n++] = {co:"Hun", ll:"4615N 2010E", lv:"2", ci:"Szeged"};
cit[n++] = {co:"Hun", ll:"4712N 1825E", lv:"2", ci:"Sz&eacute;kesfeh&eacute;rv&#225;r"};
cit[n++] = {co:"Hun", ll:"4639N 2016E", lv:"2", ci:"Szentes"};

cit[n++] = {co:"Irn", ll:"3448N 4831E", lv:"2", ci:"Hamadan"};
cit[n++] = {co:"Irn", ll:"3239N 5141E", lv:"2", ci:"Isfahan"};
cit[n++] = {co:"Irn", ll:"3359N 5126E", lv:"2", ci:"Kashan"};
cit[n++] = {co:"Irn", ll:"3816N 5936E", lv:"2", ci:"Mashad"};
cit[n++] = {co:"Irn", ll:"2937N 5232E", lv:"2", ci:"Shiraz"};
cit[n++] = {co:"Irn", ll:"3542N 5125E", lv:"2", ci:"Teheran"};

cit[n++] = {co:"Ita", ll:"4346N 1115E", lv:"2", ci:"Firenze (Florence)"};
cit[n++] = {co:"Ita", ll:"4333N 1019E", lv:"2", ci:"Livorno (Leghorn)"};
cit[n++] = {co:"Ita", ll:"4528N 0912E", lv:"2", ci:"Milano (Milan)"};
cit[n++] = {co:"Ita", ll:"4050N 1415E", lv:"2", ci:"Napoli (Naples)"};
cit[n++] = {co:"Ita", ll:"3807N 1322E", lv:"2", ci:"Palermo"};
cit[n++] = {co:"Ita", ll:"4154N 1229E", lv:"2", ci:"Roma (Rome)"};
cit[n++] = {co:"Ita", ll:"4503N 0740E", lv:"2", ci:"Torino (Turin)"};
cit[n++] = {co:"Ita", ll:"4526N 1220E", lv:"2", ci:"Venezia (Venice)"};

cit[n++] = {co:"Lat", ll:"5553N 2632E", lv:"2", ci:"Daugavpils (Dvinsk)"};
cit[n++] = {co:"Lat", ll:"5639N 2342E", lv:"2", ci:"Jelgava (Mitau)"};
cit[n++] = {co:"Lat", ll:"5631N 2101E", lv:"2", ci:"Liep&#257;ja (Libau)"};
cit[n++] = {co:"Lat", ll:"5630N 2719E", lv:"2", ci:"R&#275;zekne (Rezhitsa)"};
cit[n++] = {co:"Lat", ll:"5657N 2406E", lv:"2", ci:"R&#299;ga"};

cit[n++] = {co:"Lit", ll:"5424N 2403E", lv:"2", ci:"Alytus (Olita)"};
cit[n++] = {co:"Lit", ll:"5612N 2445E", lv:"2", ci:"Bir&#382;ai (Birzh)"};
cit[n++] = {co:"Lit", ll:"5505N 2417E", lv:"2", ci:"Jonava (Yonava)"};
cit[n++] = {co:"Lit", ll:"5424N 2314E", lv:"2", ci:"Kalvarija (Kalwarja)"};
cit[n++] = {co:"Lit", ll:"5454N 2354E", lv:"2", ci:"Kaunas (Kovno)"};
cit[n++] = {co:"Lit", ll:"5517N 2358E", lv:"2", ci:"K&#279;dainiai (Keidan)"};
cit[n++] = {co:"Lit", ll:"5543N 2107E", lv:"2", ci:"Klaip&#279;da (Memel)"};
cit[n++] = {co:"Lit", ll:"5550N 2458E", lv:"2", ci:"Kupi&#353;kis (Kupishuk)"};
cit[n++] = {co:"Lit", ll:"5434N 2321E", lv:"2", ci:"Marijampol&#279; (Maryampol)"};
cit[n++] = {co:"Lit", ll:"5544N 2421E", lv:"2", ci:"Panev&#279;&#382;ys (Ponevezh)"};
cit[n++] = {co:"Lit", ll:"5555N 2151E", lv:"2", ci:"Plung&#279; (Plungian)"};
cit[n++] = {co:"Lit", ll:"5522N 2307E", lv:"2", ci:"Raseiniai (Rasayn)"};
cit[n++] = {co:"Lit", ll:"5556N 2319E", lv:"2", ci:"&#352;iauliai (Shavl)"};
cit[n++] = {co:"Lit", ll:"5509N 2610E", lv:"2", ci:"&#352;vencionys (Sventzion)"};
cit[n++] = {co:"Lit", ll:"5515N 2217E", lv:"2", ci:"Taurag&#279; (Tavrig)"};
cit[n++] = {co:"Lit", ll:"5559N 2215E", lv:"2", ci:"Tel&#353;iai (Telz)"};
cit[n++] = {co:"Lit", ll:"5438N 2456E", lv:"2", ci:"Trakai (Troki)"};
cit[n++] = {co:"Lit", ll:"5515N 2445E", lv:"2", ci:"Ukmerg&#279; (Vilkomir)"};
cit[n++] = {co:"Lit", ll:"5530N 2536E", lv:"2", ci:"Utena (Utian)"};
cit[n++] = {co:"Lit", ll:"5441N 2519E", lv:"2", ci:"Vilnius (Vilna)"};
cit[n++] = {co:"Lit", ll:"5621N 2315E", lv:"2", ci:"&#381;agar&#279; (Zhager)"};
cit[n++] = {co:"Lit", ll:"5544N 2615E", lv:"2", ci:"Zarasai (Novo-Aleksandrovsk)"};

cit[n++] = {co:"Mac", ll:"4102N 2120E", lv:"2", ci:"Bitola (Monastir)"};
cit[n++] = {co:"Mac", ll:"4200N 2126E", lv:"2", ci:"Skopje (Usk&uuml;b)"};

cit[n++] = {co:"Mol", ll:"4746N 2756E", lv:"2", ci:"B&#259;l&#355;i (Beltsy)"};
cit[n++] = {co:"Mol", ll:"4650N 2927E", lv:"2", ci:"Bender (Tighina)"};
cit[n++] = {co:"Mol", ll:"4822N 2705E", lv:"2", ci:"Briceni (Brichany)"};
cit[n++] = {co:"Mol", ll:"4700N 2851E", lv:"2", ci:"Chi&#351;in&#259;u (Kishinev)"};
cit[n++] = {co:"Mol", ll:"4810N 2718E", lv:"2", ci:"Edine&#355; (Yedintsy)"};
cit[n++] = {co:"Mol", ll:"4816N 2648E", lv:"2", ci:"Lipcani (Lipkany)"};
cit[n++] = {co:"Mol", ll:"4723N 2849E", lv:"2", ci:"Orhei (Orgeyev)"};
cit[n++] = {co:"Mol", ll:"4809N 2818E", lv:"2", ci:"Soroca (Soroki)"};
cit[n++] = {co:"Mol", ll:"4650N 2939E", lv:"2", ci:"Tiraspol"};

cit[n++] = {co:"Mor", ll:"3403N 0459W", lv:"2", ci:"Fez"};
cit[n++] = {co:"Mor", ll:"3335N 0737W", lv:"2", ci:"Casablanca"};
cit[n++] = {co:"Mor", ll:"3138N 0800W", lv:"2", ci:"Marrakech"};
cit[n++] = {co:"Mor", ll:"3401N 0650W", lv:"2", ci:"Rabat"};

cit[n++] = {co:"Net", ll:"5221N 0455E", lv:"2", ci:"Amsterdam"};
cit[n++] = {co:"Net", ll:"5205N 0418E", lv:"2", ci:"Den Haag"};
cit[n++] = {co:"Net", ll:"5313N 0633E", lv:"2", ci:"Groningen"};
cit[n++] = {co:"Net", ll:"5312N 0547E", lv:"2", ci:"Leeuwarden"};
cit[n++] = {co:"Net", ll:"5155N 0430E", lv:"2", ci:"Rotterdam"};
cit[n++] = {co:"Net", ll:"5205N 0508E", lv:"2", ci:"Utrecht"};

cit[n++] = {co:"Pol", ll:"           ", lv:"1", ci:"Galicia:"};
cit[n++] = {co:"Pol", ll:"4949N 1902E", lv:"2", ci:"Bielsko-Bia&#322;a"};
cit[n++] = {co:"Pol", ll:"5005N 1955E", lv:"2", ci:"Krak&#243;w"};
cit[n++] = {co:"Pol", ll:"4947N 2247E", lv:"2", ci:"Przemy&#347;l (Pshemishl)"};
cit[n++] = {co:"Pol", ll:"5003N 2200E", lv:"2", ci:"Rzesz&#243;w (Zheshuv)"};
cit[n++] = {co:"Pol", ll:"5001N 2059E", lv:"2", ci:"Tarn&#243;w"};
cit[n++] = {co:"Pol", ll:"           ", lv:"1", ci:"Prussia:"};
cit[n++] = {co:"Pol", ll:"5309N 1800E", lv:"2", ci:"Bydgoszcz (Bromberg)"};
cit[n++] = {co:"Pol", ll:"5421N 1840E", lv:"2", ci:"Gda&#324;sk (Danzig)"};
cit[n++] = {co:"Pol", ll:"5225N 1658E", lv:"2", ci:"Pozna&#324; (Posen)"};
cit[n++] = {co:"Pol", ll:"5325N 1435E", lv:"2", ci:"Szczecin (Stettin)"};
cit[n++] = {co:"Pol", ll:"5106N 1702E", lv:"2", ci:"Wroc&#322;aw (Breslau)"};
cit[n++] = {co:"Pol", ll:"           ", lv:"1", ci:"Russian Empire:"};
cit[n++] = {co:"Pol", ll:"5020N 1909E", lv:"2", ci:"B&#281;dzin (Bendin)"};
cit[n++] = {co:"Pol", ll:"5308N 2309E", lv:"2", ci:"Bia&#322;ystok"};
cit[n++] = {co:"Pol", ll:"5048N 1907E", lv:"2", ci:"Cz&#281;stochowa"};
cit[n++] = {co:"Pol", ll:"5145N 1805E", lv:"2", ci:"Kalisz"};
cit[n++] = {co:"Pol", ll:"5050N 2040E", lv:"2", ci:"Kielce (Kieltz)"};
cit[n++] = {co:"Pol", ll:"5145N 1928E", lv:"2", ci:"&#321;&#243;d&#378;"};
cit[n++] = {co:"Pol", ll:"5311N 2205E", lv:"2", ci:"&#321;om&#380;a (Lomzhe)"};
cit[n++] = {co:"Pol", ll:"5115N 2234E", lv:"2", ci:"Lublin"};
cit[n++] = {co:"Pol", ll:"5233N 1942E", lv:"2", ci:"P&#322;ock (Plotsk)"};
cit[n++] = {co:"Pol", ll:"5124N 1941E", lv:"2", ci:"Piotrk&#243;w Trybunalski"};
cit[n++] = {co:"Pol", ll:"5125N 2109E", lv:"2", ci:"Radom"};
cit[n++] = {co:"Pol", ll:"5210N 2218E", lv:"2", ci:"Siedlce (Shedlits)"};
cit[n++] = {co:"Pol", ll:"5406N 2256E", lv:"2", ci:"Suwa&#322;ki (Suvalk)"};
cit[n++] = {co:"Pol", ll:"5215N 2100E", lv:"2", ci:"Warszawa (Warsaw)"};

cit[n++] = {co:"Rom", ll:"           ", lv:"1", ci:"Bukovina:"};
cit[n++] = {co:"Rom", ll:"4751N 2555E", lv:"2", ci:"R&#259;d&#259;u&#355;i (Radautz)"};
cit[n++] = {co:"Rom", ll:"4738N 2615E", lv:"2", ci:"Suceava"};
cit[n++] = {co:"Rom", ll:"           ", lv:"1", ci:"Dobruja:"};
cit[n++] = {co:"Rom", ll:"4411N 2839E", lv:"2", ci:"Constan&#355;a"};
cit[n++] = {co:"Rom", ll:"4510N 2848E", lv:"2", ci:"Tulcea"};
cit[n++] = {co:"Rom", ll:"           ", lv:"1", ci:"Moldavia:"};
cit[n++] = {co:"Rom", ll:"4634N 2654E", lv:"2", ci:"Bac&#259;u"};
cit[n++] = {co:"Rom", ll:"4614N 2740E", lv:"2", ci:"B&#238;rlad"};
cit[n++] = {co:"Rom", ll:"4745N 2640E", lv:"2", ci:"Boto&#351;ani"};
cit[n++] = {co:"Rom", ll:"4757N 2624E", lv:"2", ci:"Dorohoi"};
cit[n++] = {co:"Rom", ll:"4727N 2618E", lv:"2", ci:"F&#259;lticeni"};
cit[n++] = {co:"Rom", ll:"4542N 2711E", lv:"2", ci:"Foc&#351;ani"};
cit[n++] = {co:"Rom", ll:"4527N 2803E", lv:"2", ci:"Gala&#355;i (Galatz)"};
cit[n++] = {co:"Rom", ll:"4641N 2804E", lv:"2", ci:"Hu&#351;i"};
cit[n++] = {co:"Rom", ll:"4710N 2736E", lv:"2", ci:"Ia&#351;i (Yash)"};
cit[n++] = {co:"Rom", ll:"4655N 2620E", lv:"2", ci:"Piatra Neam&#355;"};
cit[n++] = {co:"Rom", ll:"4655N 2655E", lv:"2", ci:"Roman"};
cit[n++] = {co:"Rom", ll:"4638N 2744E", lv:"2", ci:"Vaslui"};
cit[n++] = {co:"Rom", ll:"           ", lv:"1", ci:"Transylvania:"};
cit[n++] = {co:"Rom", ll:"4611N 2119E", lv:"2", ci:"Arad"};
cit[n++] = {co:"Rom", ll:"4740N 2335E", lv:"2", ci:"Baia Mare (Nagyb&#225;na)"};
cit[n++] = {co:"Rom", ll:"4538N 2535E", lv:"2", ci:"Bra&#351;ov (Brass&#243;)"};
cit[n++] = {co:"Rom", ll:"4646N 2336E", lv:"2", ci:"Cluj-Napoca (Kolozsv&#225;r)"};
cit[n++] = {co:"Rom", ll:"4704N 2156E", lv:"2", ci:"Oradea (Nagy-V&#225;rad)"};
cit[n++] = {co:"Rom", ll:"4748N 2253E", lv:"2", ci:"Satu Mare (Szatm&#225;r)"};
cit[n++] = {co:"Rom", ll:"4756N 2353E", lv:"2", ci:"Sighetu Marma&#355;iei (Sziget)"};
cit[n++] = {co:"Rom", ll:"4546N 2114E", lv:"2", ci:"Timi&#351;oara (Temesv&#225;r)"};
cit[n++] = {co:"Rom", ll:"           ", lv:"1", ci:"Walachia:"};
cit[n++] = {co:"Rom", ll:"4516N 2759E", lv:"2", ci:"Br&#259;ila"};
cit[n++] = {co:"Rom", ll:"4426N 2606E", lv:"2", ci:"Bucure&#351;ti (Bucharest)"};
cit[n++] = {co:"Rom", ll:"4419N 2348E", lv:"2", ci:"Craiova"};
cit[n++] = {co:"Rom", ll:"4457N 2601E", lv:"2", ci:"Ploie&#351;ti"};

cit[n++] = {co:"Rus", ll:"5315N 3421E", lv:"2", ci:"Bryansk"};
cit[n++] = {co:"Rus", ll:"5443N 2030E", lv:"2", ci:"Kaliningrad (K&ouml;nigsberg)"};
cit[n++] = {co:"Rus", ll:"5545N 3735E", lv:"2", ci:"Moskva (Moscow)"};
cit[n++] = {co:"Rus", ll:"5602N 2955E", lv:"2", ci:"Nevel"};
cit[n++] = {co:"Rus", ll:"5232N 3156E", lv:"2", ci:"Novozybkov"};
cit[n++] = {co:"Rus", ll:"4714N 3943E", lv:"2", ci:"Rostov-na-Donu"};
cit[n++] = {co:"Rus", ll:"5954N 3016E", lv:"2", ci:"Sankt-Peterburg (Leningrad)"};
cit[n++] = {co:"Rus", ll:"5134N 4602E", lv:"2", ci:"Saratov"};
cit[n++] = {co:"Rus", ll:"5447N 3202E", lv:"2", ci:"Smolensk"};
cit[n++] = {co:"Rus", ll:"5235N 3246E", lv:"2", ci:"Starodub"};
cit[n++] = {co:"Rus", ll:"5536N 3112E", lv:"2", ci:"Velizh"};

cit[n++] = {co:"Ser", ll:"4449N 2028E", lv:"2", ci:"Beograd (Belgrade)"};
cit[n++] = {co:"Ser", ll:"4515N 1950E", lv:"2", ci:"Novi Sad (&Uacute;jvidek)"};
cit[n++] = {co:"Ser", ll:"4556N 2005E", lv:"2", ci:"Senta (Zenta) "};
cit[n++] = {co:"Ser", ll:"4546N 1907E", lv:"2", ci:"Sombor (Zombor)"};
cit[n++] = {co:"Ser", ll:"4606N 1940E", lv:"2", ci:"Subotica (Szabadka)"};

cit[n++] = {co:"Slo", ll:"4809N 1707E", lv:"2", ci:"Bratislava (Pressburg)"};
cit[n++] = {co:"Slo", ll:"4856N 2155E", lv:"2", ci:"Humenn&eacute; (Homonna)"};
cit[n++] = {co:"Slo", ll:"4843N 2115E", lv:"2", ci:"Ko&#353;ice (Kassa)"};
cit[n++] = {co:"Slo", ll:"4845N 2156E", lv:"2", ci:"Michalovce (Nagymih&#225;ly)"};
cit[n++] = {co:"Slo", ll:"4819N 1805E", lv:"2", ci:"Nitra (Nyitra)"};
cit[n++] = {co:"Slo", ll:"4900N 2115E", lv:"2", ci:"Pre&#353;ov (Eperjes)"};
cit[n++] = {co:"Slo", ll:"4912N 2139E", lv:"2", ci:"Stropkov (Sztropk&#243;)"};
cit[n++] = {co:"Slo", ll:"4853N 1803E", lv:"2", ci:"Trencín (Trencsén)"};

cit[n++] = {co:"Tun", ll:"3353N 1007E", lv:"2", ci:"Gab&egrave;s"};
cit[n++] = {co:"Tun", ll:"3444N 1046E", lv:"2", ci:"Sfax"};
cit[n++] = {co:"Tun", ll:"3550N 1038E", lv:"2", ci:"Sousse"};
cit[n++] = {co:"Tun", ll:"3648N 1011E", lv:"2", ci:"Tunis"};

cit[n++] = {co:"Tur", ll:"3956N 3252E", lv:"2", ci:"Ankara"};
cit[n++] = {co:"Tur", ll:"4011N 2904E", lv:"2", ci:"Bursa"};
cit[n++] = {co:"Tur", ll:"4009N 2624E", lv:"2", ci:"&#199;anakkale (Dardanelles)"};
cit[n++] = {co:"Tur", ll:"4140N 2634E", lv:"2", ci:"Edirne (Adrianople)"};
cit[n++] = {co:"Tur", ll:"4024N 2640E", lv:"2", ci:"Gelibolu (Gallipoli)"};
cit[n++] = {co:"Tur", ll:"4101N 2858E", lv:"2", ci:"&#304;stanbul"};
cit[n++] = {co:"Tur", ll:"3825N 2709E", lv:"2", ci:"&#304;zmir (Smyrna)"};
cit[n++] = {co:"Tur", ll:"3836N 2726E", lv:"2", ci:"Manisa"};
cit[n++] = {co:"Tur", ll:"4104N 2815E", lv:"2", ci:"Silivri (Selymbria)"};

cit[n++] = {co:"Ukr", ll:"           ", lv:"1", ci:"Bukovina:"};
cit[n++] = {co:"Ukr", ll:"4818N 2556E", lv:"2", ci:"Chernivtsi (Tschernowitz)"};
cit[n++] = {co:"Ukr", ll:"           ", lv:"1", ci:"Galicia:"};
cit[n++] = {co:"Ukr", ll:"5005N 2509E", lv:"2", ci:"Brody (Brod)"};
cit[n++] = {co:"Ukr", ll:"4905N 2524E", lv:"2", ci:"Buchach (Buczacz)"};
cit[n++] = {co:"Ukr", ll:"4921N 2330E", lv:"2", ci:"Drogobych (Drohobycz)"};
cit[n++] = {co:"Ukr", ll:"4856N 2443E", lv:"2", ci:"Ivano-Frankivsk (Stanis&#322;aw&#243;w)"};
cit[n++] = {co:"Ukr", ll:"4832N 2502E", lv:"2", ci:"Kolomyya (Ko&#322;omyja)"};
cit[n++] = {co:"Ukr", ll:"4950N 2400E", lv:"2", ci:"L'viv (Lvov)"};
cit[n++] = {co:"Ukr", ll:"4858N 2526E", lv:"2", ci:"Pomortsy (Jaz&#322;owiec)"};
cit[n++] = {co:"Ukr", ll:"5015N 2337E", lv:"2", ci:"Rava-Ruska (Rawa Ruska)"};
cit[n++] = {co:"Ukr", ll:"4915N 2351E", lv:"2", ci:"Stryy (Stryj)"};
cit[n++] = {co:"Ukr", ll:"4933N 2535E", lv:"2", ci:"Ternopil (Tarnopol)"};
cit[n++] = {co:"Ukr", ll:"5004N 2358E", lv:"2", ci:"Zhovkva (Nesterov, &#379;&#243;&#322;kiew)"};
cit[n++] = {co:"Ukr", ll:"           ", lv:"1", ci:"Russian Empire:"};
cit[n++] = {co:"Ukr", ll:"4756N 2937E", lv:"2", ci:"Balta"};
cit[n++] = {co:"Ukr", ll:"4954N 2835E", lv:"2", ci:"Berdychiv (Berdichev)"};
cit[n++] = {co:"Ukr", ll:"4947N 3007E", lv:"2", ci:"Bila Tserkva (Belaya Tserkov)"};
cit[n++] = {co:"Ukr", ll:"4612N 3021E", lv:"2", ci:"Bilhorod-Dnistrovskyy (Belgorod)"};
cit[n++] = {co:"Ukr", ll:"4926N 3204E", lv:"2", ci:"Cherkasy"};
cit[n++] = {co:"Ukr", ll:"5130N 3118E", lv:"2", ci:"Chernihiv (Chernigov)"};
cit[n++] = {co:"Ukr", ll:"4827N 3459E", lv:"2", ci:"Dnipropetrovsk (Ekaterinoslav)"};
cit[n++] = {co:"Ukr", ll:"4521N 2850E", lv:"2", ci:"Izmayil (Izmail)"};
cit[n++] = {co:"Ukr", ll:"4840N 2634E", lv:"2", ci:"Kamyanets-Podilskyy"};
cit[n++] = {co:"Ukr", ll:"5000N 3615E", lv:"2", ci:"Kharkiv (Charkov)"};
cit[n++] = {co:"Ukr", ll:"4638N 3236E", lv:"2", ci:"Kherson"};
cit[n++] = {co:"Ukr", ll:"4925N 2700E", lv:"2", ci:"Khmelnytskyy (Proskurov)"};
cit[n++] = {co:"Ukr", ll:"4829N 2630E", lv:"2", ci:"Khotin"};
cit[n++] = {co:"Ukr", ll:"4830N 3216E", lv:"2", ci:"Kirovohrad (Elizavetgrad)"};
cit[n++] = {co:"Ukr", ll:"5113N 2443E", lv:"2", ci:"Kovel (Kowel)"};
cit[n++] = {co:"Ukr", ll:"5006N 2543E", lv:"2", ci:"Kremenets (Krzemieniec)"};
cit[n++] = {co:"Ukr", ll:"5026N 3031E", lv:"2", ci:"Kyyiv (Kiev)"};
cit[n++] = {co:"Ukr", ll:"5045N 2520E", lv:"2", ci:"Lutsk (&#321;uck)"};
cit[n++] = {co:"Ukr", ll:"4827N 2748E", lv:"2", ci:"Mohyliv-Podilskyy"};
cit[n++] = {co:"Ukr", ll:"4658N 3200E", lv:"2", ci:"Mykolayiv (Nikolayev)"};
cit[n++] = {co:"Ukr", ll:"4628N 3044E", lv:"2", ci:"Odesa"};
cit[n++] = {co:"Ukr", ll:"4935N 3434E", lv:"2", ci:"Poltava"};
cit[n++] = {co:"Ukr", ll:"5037N 2615E", lv:"2", ci:"Rivne (Rovno)"};
cit[n++] = {co:"Ukr", ll:"4845N 3013E", lv:"2", ci:"Uman"};
cit[n++] = {co:"Ukr", ll:"4914N 2829E", lv:"2", ci:"Vinnytsya (Vinnitsa)"};
cit[n++] = {co:"Ukr", ll:"4815N 2817E", lv:"2", ci:"Yampol (Iampil)"};
cit[n++] = {co:"Ukr", ll:"5015N 2840E", lv:"2", ci:"Zhytomyr (Zhitomir)"};
cit[n++] = {co:"Ukr", ll:"           ", lv:"1", ci:"Subcarpathia:"};
cit[n++] = {co:"Ukr", ll:"4811N 2318E", lv:"2", ci:"Khust (Huszt)"};
cit[n++] = {co:"Ukr", ll:"4827N 2243E", lv:"2", ci:"Mukacheve (Munk&#225;cs)"};
cit[n++] = {co:"Ukr", ll:"4837N 2218E", lv:"2", ci:"Uzhhorod (Ungv&#225;r)"};
}