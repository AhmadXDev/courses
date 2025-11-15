import 'package:flutter/material.dart';
import 'package:dash_chat_2/dash_chat_2.dart';
import 'package:gpt_api/services/gpt.dart';

class ChatPage extends StatefulWidget {
  const ChatPage({super.key});

  @override
  State<ChatPage> createState() => _ChatPageState();
}

class _ChatPageState extends State<ChatPage> {
  ChatUser me = ChatUser(id: "1");
  ChatUser bot = ChatUser(id: "2");

  String? apiKey;

  List<ChatMessage> messageList = [];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      showApiKeyDialog();
    });
  }

  void showApiKeyDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        TextEditingController apiKeyController = TextEditingController();
        return AlertDialog(
          title: const Text('Enter API Key'),
          content: TextField(
            controller: apiKeyController,
            decoration: const InputDecoration(hintText: "API Key"),
          ),
          actions: <Widget>[
            TextButton(
              child: const Text('Submit'),
              onPressed: () {
                setState(() {
                  apiKey = apiKeyController.text;
                });
                Navigator.of(context).pop();
              },
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: apiKey == null
          ? const Center(child: CircularProgressIndicator())
          : DashChat(
              currentUser: me,
              onSend: (newMessage) async {
                messageList.insert(0, newMessage);
                setState(() {});

                String content = await Gpt().getData(newMessage.text, apiKey!);

                ChatMessage botMessage = ChatMessage(
                  user: bot,
                  text: content,
                  createdAt: DateTime.now(),
                );
                messageList.insert(0, botMessage);
                setState(() {});
              },
              messages: messageList,
            ),
    );
  }
}
