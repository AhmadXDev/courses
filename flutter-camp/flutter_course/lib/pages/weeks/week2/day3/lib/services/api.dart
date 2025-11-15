import '../models/character.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../data/global_data.dart';

class Api {
  String link = "https://api.disneyapi.dev/character";

  getData() async {
    var uri = Uri.parse(link);
    var response = await http.get(uri);

    var responseJson = json.decode(response.body);

    for (var element in responseJson["data"]) {
      allCharacters.add(Character.fromJson(element));
    }
  }
}
