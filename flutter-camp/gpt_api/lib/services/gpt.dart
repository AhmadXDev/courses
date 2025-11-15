// ignore_for_file: avoid_print

import 'dart:convert';
import 'package:http/http.dart' as http;

class Gpt {
  Future<String> getData(String prompt, String key) async {
    String link = "https://api.openai.com/v1/chat/completions";
    var uri = Uri.parse(link);

    var header = {
      "Content-Type": "application/json",
      "Authorization": "Bearer $key"
    };

    var body = {
      "model": "gpt-3.5-turbo",
      "messages": [
        {"role": "system", "content": "You are a helpful assistant."},
        {"role": "user", "content": prompt}
      ]
    };

    // encode: Dart object → JSON string (for sending data).
    // decode: JSON string → Dart object (for reading received data).

    var request =
        await http.post(uri, headers: header, body: json.encode(body));
    var response = json.decode(request.body);
    String content = response["choices"][0]["message"]["content"];
    return content;
  }
}
