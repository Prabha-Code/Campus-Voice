from flask import Flask, request, jsonify
import google.generativeai as genai
from flask_cors import CORS


app = Flask(__name__)

CORS(app)  # Allow all origins


# Configure the API
api_key = "AIzaSyBusmdXcpH2qvFtwH3sSAP6mZq3m1noFu8"  # Replace with your actual API key
genai.configure(api_key=api_key)

# Create the model
generation_config = {
    "temperature": 1,
    "top_p": 0.95,
    "top_k": 40,
    "max_output_tokens": 8192,
    "response_mime_type": "text/plain",
}

model = genai.GenerativeModel(
    model_name="gemini-1.5-flash",
    generation_config=generation_config,
)

chat_session = model.start_chat(history=[])

# Predefined stored questions and answers
stored_qa = {
    "What is your name?": "I am a chatbot powered by Gemini AI.",
    "How does AI work?": "AI works by using algorithms and data to perform tasks that usually require human intelligence.",
    "Who created you?": "I was created by Surya and Sudharsanam.",
    "How do I submit a complaint?": "You can submit a complaint by visiting the complaints portal and filling out the form with the necessary details. Ensure you provide a clear description and attach any relevant documents if needed.",
    "Where can I register my complaint?": "You can register your complaint online through our complaints portal or visit the respective department’s office for offline submission.",
    "Can I submit an anonymous complaint?": "Yes, you can choose to submit your complaint anonymously. However, keep in mind that we may not be able to update you on the status of your complaint if you don’t provide contact details.",
    "How can I track my complaint status?": "You can track your complaint by logging into your account and navigating to the 'My Complaints' section. If you provided an email, you will also receive status updates.",
    "What is the expected resolution time for my complaint?": "The resolution time varies based on the nature of the complaint. Generally, complaints are resolved within [X] days, but urgent matters are prioritized.",
    "Can I update or modify my complaint after submission?": "Once submitted, complaints cannot be modified. However, you can submit a follow-up request with additional details.",
    "Will I receive a confirmation after submitting a complaint?": "Yes, once your complaint is successfully submitted, you will receive a confirmation email/SMS with a unique tracking ID.",
    "How do I provide feedback about a department?": "You can submit feedback through the feedback portal by selecting the relevant department and entering your comments.",
    "Can I give feedback without revealing my identity?": "Yes, anonymous feedback is allowed. However, providing your details can help us follow up on your suggestions.",
    "Will my feedback be considered for future improvements?": "Absolutely! All feedback is reviewed by the concerned department to improve services and facilities.",
    "How do I file a complaint regarding hostel facilities?": "You can submit a complaint under the 'Hostel' category in the complaints portal. Please provide specific details such as block name and room number.",
    "Where can I report a mess food quality issue?": "You can report food-related complaints under the 'Mess' category. Attaching photos can help us assess the issue faster.",
    "who is tharkoori?": "His name is Sree Vishal",
    "Can I submit complaints about faculty or staff behavior?": "Yes, you can report concerns regarding faculty or staff behavior. Your complaint will be reviewed confidentially by the respective authorities.",
    "My complaint is not resolved. What should I do?": "If your complaint is unresolved, you can escalate it by selecting the 'Escalate Complaint' option in the tracking section or contacting the grievance officer.",
    "What happens if my complaint is ignored?": "Complaints are monitored by a higher authority. If unresolved, you can escalate it to the department head or management.",
    "Can I check the response history of my complaint?": "Yes, you can view all responses and updates in the 'My Complaints' section on your dashboard.",
    "What types of complaints are accepted?": "We accept complaints related to academics, hostel facilities, mess services, faculty behavior, technical issues, and other university-related concerns.",
    "Are complaints confidential?": "Yes, all complaints are handled with confidentiality. Your identity will not be disclosed without consent.",
    "Can I withdraw my complaint after submission?": "Yes, you can request a complaint withdrawal if it has not been acted upon yet.",
    "I’m unable to log in. What should I do?": "Try resetting your password using the 'Forgot Password' option. If the issue persists, contact technical support at support@example.com.",
    "My complaint form is not submitting. How can I fix this?": "Ensure all required fields are filled. If the issue continues, try refreshing the page or using a different browser.",
    "I didn’t receive a confirmation email. What should I do?": "Check your spam/junk folder. If you still don’t see it, contact support for assistance.",


    "what is your name?": "i am a chatbot powered by gemini ai.",
    "how does ai work?": "ai works by using algorithms and data to perform tasks that usually require human intelligence.",
    "who created you?": "i was created by surya and sudharsanam.",
    "how do i submit a complaint?": "you can submit a complaint by visiting the complaints portal and filling out the form with the necessary details. ensure you provide a clear description and attach any relevant documents if needed.",
    "where can i register my complaint?": "you can register your complaint online through our complaints portal or visit the respective department’s office for offline submission.",
    "can i submit an anonymous complaint?": "yes, you can choose to submit your complaint anonymously. however, keep in mind that we may not be able to update you on the status of your complaint if you don’t provide contact details.",
    "how can i track my complaint status?": "you can track your complaint by logging into your account and navigating to the 'my complaints' section. if you provided an email, you will also receive status updates.",
    "what is the expected resolution time for my complaint?": "the resolution time varies based on the nature of the complaint. generally, complaints are resolved within [x] days, but urgent matters are prioritized.",
    "can i update or modify my complaint after submission?": "once submitted, complaints cannot be modified. however, you can submit a follow-up request with additional details.",
    "will i receive a confirmation after submitting a complaint?": "yes, once your complaint is successfully submitted, you will receive a confirmation email/sms with a unique tracking id.",
    "how do i provide feedback about a department?": "you can submit feedback through the feedback portal by selecting the relevant department and entering your comments.",
    "can i give feedback without revealing my identity?": "yes, anonymous feedback is allowed. however, providing your details can help us follow up on your suggestions.",
    "will my feedback be considered for future improvements?": "absolutely! all feedback is reviewed by the concerned department to improve services and facilities.",
    "how do i file a complaint regarding hostel facilities?": "you can submit a complaint under the 'hostel' category in the complaints portal. please provide specific details such as block name and room number.",
    "where can i report a mess food quality issue?": "you can report food-related complaints under the 'mess' category. attaching photos can help us assess the issue faster.",
    "who is tharkoori?": "his name is shree vishal",
    "can i submit complaints about faculty or staff behavior?": "yes, you can report concerns regarding faculty or staff behavior. your complaint will be reviewed confidentially by the respective authorities.",
    "my complaint is not resolved. what should i do?": "if your complaint is unresolved, you can escalate it by selecting the 'escalate complaint' option in the tracking section or contacting the grievance officer.",
    "what happens if my complaint is ignored?": "complaints are monitored by a higher authority. if unresolved, you can escalate it to the department head or management.",
    "can i check the response history of my complaint?": "yes, you can view all responses and updates in the 'my complaints' section on your dashboard.",
    "what types of complaints are accepted?": "we accept complaints related to academics, hostel facilities, mess services, faculty behavior, technical issues, and other university-related concerns.",   
    "are complaints confidential?": "yes, all complaints are handled with confidentiality. your identity will not be disclosed without consent.",
    "can i withdraw my complaint after submission?": "yes, you can request a complaint withdrawal if it has not been acted upon yet.",
    "i'm unable to log in. what should i do?": "try resetting your password using the 'forgot password' option. if the issue persists, contact technical support at support@example.com.",
    "who created you?": "i was created by INNOVATEHUB.",
    "who created you": "I was created by INNOVATEHUB.",
    "whocreatedyou?": "i was created by sudharsanam and surya.",
    "whocreatedyou": "I was created by INNOVATEHUB.",
    "my complaint form is not submitting. how can i fix this?": "ensure all required fields are filled. if the issue continues, try refreshing the page or using a different browser.",
    "i didn't receive a confirmation email. what should i do?": "check your spam/junk folder. if you still don’t see it, contact support for assistance.",


    "What is your name": "I am a chatbot powered by Gemini AI.",
    "How does AI work": "AI works by using algorithms and data to perform tasks that usually require human intelligence.",
    "How do I submit a complaint": "You can submit a complaint by visiting the complaints portal and filling out the form with the necessary details. Ensure you provide a clear description and attach any relevant documents if needed.",
    "Where can I register my complaint": "You can register your complaint online through our complaints portal or visit the respective department’s office for offline submission.",
    "Can I submit an anonymous complaint": "Yes, you can choose to submit your complaint anonymously. However, keep in mind that we may not be able to update you on the status of your complaint if you don’t provide contact details.",
    "How can I track my complaint status": "You can track your complaint by logging into your account and navigating to the 'My Complaints' section. If you provided an email, you will also receive status updates.",
    "What is the expected resolution time for my complaint": "The resolution time varies based on the nature of the complaint. Generally, complaints are resolved within [X] days, but urgent matters are prioritized.",
    "Can I update or modify my complaint after submission": "Once submitted, complaints cannot be modified. However, you can submit a follow-up request with additional details.",
    "Will I receive a confirmation after submitting a complaint": "Yes, once your complaint is successfully submitted, you will receive a confirmation email/SMS with a unique tracking ID.",
    "How do I provide feedback about a department": "You can submit feedback through the feedback portal by selecting the relevant department and entering your comments.",
    "Can I give feedback without revealing my identity": "Yes, anonymous feedback is allowed. However, providing your details can help us follow up on your suggestions.",
    "Will my feedback be considered for future improvements": "Absolutely! All feedback is reviewed by the concerned department to improve services and facilities.",
    "How do I file a complaint regarding hostel facilities": "You can submit a complaint under the 'Hostel' category in the complaints portal. Please provide specific details such as block name and room number.",
    "Where can I report a mess food quality issue": "You can report food-related complaints under the 'Mess' category. Attaching photos can help us assess the issue faster.",
    "Who is tharkoori": "His name is Shree Vishal",
    "Can I submit complaints about faculty or staff behavior": "Yes, you can report concerns regarding faculty or staff behavior. Your complaint will be reviewed confidentially by the respective authorities.",
    "My complaint is not resolved. What should I do": "If your complaint is unresolved, you can escalate it by selecting the 'Escalate Complaint' option in the tracking section or contacting the grievance officer.",
    "What happens if my complaint is ignored": "Complaints are monitored by a higher authority. If unresolved, you can escalate it to the department head or management.",
    "Can I check the response history of my complaint": "Yes, you can view all responses and updates in the 'My Complaints' section on your dashboard.",


    "what is your name?" : "i am a chatbot powered by gemini ai.",
    "how does ai work?" : "ai works by using algorithms and data to perform tasks that usually require human intelligence.",
    "how do i submit a complaint?" : "you can submit a complaint by visiting the complaints portal and filling out the form with the necessary details. ensure you provide a clear description and attach any relevant documents if needed.",
    "where can i register my complaint?" : "you can register your complaint online through our complaints portal or visit the respective department’s office for offline submission.",
    "can i submit an anonymous complaint?" : "yes, you can choose to submit your complaint anonymously. however, keep in mind that we may not be able to update you on the status of your complaint if you don’t provide contact details.",
    "how can i track my complaint status?" : "you can track your complaint by logging into your account and navigating to the 'my complaints' section. if you provided an email, you will also receive status updates.",
    "what is the expected resolution time for my complaint?" : "the resolution time varies based on the nature of the complaint. generally, complaints are resolved within [x] days, but urgent matters are prioritized.",
    "can i update or modify my complaint after submission?" : "once submitted, complaints cannot be modified. however, you can submit a follow-up request with additional details.",
    "will i receive a confirmation after submitting a complaint?" : "yes, once your complaint is successfully submitted, you will receive a confirmation email/sms with a unique tracking id.",
    "how do i provide feedback about a department?" : "you can submit feedback through the feedback portal by selecting the relevant department and entering your comments.",
    "can i give feedback without revealing my identity?" : "yes, anonymous feedback is allowed. however, providing your details can help us follow up on your suggestions.",
    "will my feedback be considered for future improvements?" : "absolutely! all feedback is reviewed by the concerned department to improve services and facilities.",
    "how do i file a complaint regarding hostel facilities?" : "you can submit a complaint under the 'hostel' category in the complaints portal. please provide specific details such as block name and room number.",
    "where can i report a mess food quality issue?" : "you can report food-related complaints under the 'mess' category. attaching photos can help us assess the issue faster.",
    "who is tharkoori?" : "his name is sree vishal.",
    "can i submit complaints about faculty or staff behavior?" : "yes, you can report concerns regarding faculty or staff behavior. your complaint will be reviewed confidentially by the respective authorities.",
    "my complaint is not resolved. what should i do?" : "if your complaint is unresolved, you can escalate it by selecting the 'escalate complaint' option in the tracking section or contacting the grievance officer.",
    "what happens if my complaint is ignored?" : "complaints are monitored by a higher authority. if unresolved, you can escalate it to the department head or management.",
    "can i check the response history of my complaint?" : "yes, you can view all responses and updates in the 'my complaints' section on your dashboard.",     

    
}

@app.route('/chat', methods=['POST'])
def chat():
    data = request.json
    user_message = data.get("message", "")

    if not user_message:
        return jsonify({"error": "No message provided"}), 400

    # Check predefined responses first
    if user_message in stored_qa:
        response_text = stored_qa[user_message]
    else:
        response = chat_session.send_message(user_message)
        response_text = response.text if response else "Sorry, I couldn't process that."

    return jsonify({"response": response_text})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
