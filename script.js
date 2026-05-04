let currentStoryText = "";
let currentVoiceCode = "en-US";

function openStory(story) {
  currentStoryText = story.story_text;
  currentVoiceCode = story.voice_code || "en-US";

  document.getElementById("modalImage").src = story.image_url;
  document.getElementById("modalTitle").textContent = story.title;
  document.getElementById("modalMeta").textContent =
    `${story.language_name} • ${story.icon} ${story.category_name} • Age ${story.age_group} • ${story.reading_time}`;
  document.getElementById("modalText").textContent = story.story_text;
  document.getElementById("voiceNote").textContent =
    `Voice language selected: ${currentVoiceCode}. If Telugu voice is installed, Telugu story will be spoken in Telugu.`;
  document.getElementById("storyModal").style.display = "block";
}

function closeStory() {
  stopStory();
  document.getElementById("storyModal").style.display = "none";
}

function speakStory() {
  stopStory();

  const utterance = new SpeechSynthesisUtterance(currentStoryText);
  utterance.lang = currentVoiceCode;
  utterance.rate = 0.86;
  utterance.pitch = 1.1;

  const voices = speechSynthesis.getVoices();
  const matchedVoice = voices.find(v => v.lang === currentVoiceCode || v.lang.startsWith(currentVoiceCode.split("-")[0]));
  if (matchedVoice) {
    utterance.voice = matchedVoice;
  }

  speechSynthesis.speak(utterance);
}

function pauseStory() {
  speechSynthesis.pause();
}

function resumeStory() {
  speechSynthesis.resume();
}

function stopStory() {
  speechSynthesis.cancel();
}

function toggleTheme() {
  document.body.classList.toggle("dark");
}

window.speechSynthesis.onvoiceschanged = () => {
  speechSynthesis.getVoices();
};
