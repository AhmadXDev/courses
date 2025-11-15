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

  final String key = "";

  List<ChatMessage> messageList = [];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: DashChat(
            currentUser: me,
            onSend: (newMessage) async {
              messageList.insert(0, newMessage);
              setState(() {});

              String content = await Gpt().getData(newMessage.text, key);

              ChatMessage botMessage = ChatMessage(
                user: bot,
                text: content,
                createdAt: DateTime.now(),
              );
              messageList.insert(0, botMessage);
              setState(() {});
            },
            messages: messageList));
  }
}
